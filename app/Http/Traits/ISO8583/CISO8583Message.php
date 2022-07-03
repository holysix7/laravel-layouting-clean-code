<?php
namespace App\Http\Traits\ISO8583;

use App\Http\Traits\ISO8583\ISO8583Format;
use App\Http\Traits\ISO8583\CBaseConverter;

class CISO8583Parser
{

    // internal vars
    private $sISOMsgStream = "";
    private $sMTI = "";
    private $sPrimaryBitMap = "";
    private $sSecondaryBitMap = "";
    private $sTertiaryBitMap = "";
    private $sISOVersion = "2003";
    private $aISODataElements = NULL;
    // debug
    private $bDebug = false;
    private $sLogFileName = "";
    // last error
    private $iErrCode = 0;
    private $sErrMsg = "";

    function __construct($sISOMsgStream = "")
    {
        $this->AssignVars($sISOMsgStream);
    }
// end of construct

    function AssignVars($sISOMsgStream = "")
    {
        $this->sISOMsgStream = $sISOMsgStream;
    }
// end of AssignVars

    function Debug($bDebug = false, $sLogFileName = "")
    {
        $this->bDebug = $bDebug;
        $this->sLogFileName = $sLogFileName;
    }
// end of Debug

    function GetUsedDataElement($sBitMap = "", $iWhichBitMap = 0)
    {
        //echo "[INFO] >> here 1\n";
        $Converter = new CBaseConverter;
        $sBitMapBin = $Converter->_ascii_hexa_to_bin($sBitMap);
        //echo "[INFO] >> here 2 -> sBitMapBin [$sBitMapBin]\n";
        $n = strlen($sBitMapBin);
        $iIdx = 0;
        for ($i = 0; $i < $n; $i++) {
            if ($sBitMapBin[$i] == "1") {
                $aUsed[$iIdx++] = $i + 1 + ($iWhichBitMap * 64);
            }
        }
        //echo "[INFO] >> here 3 -> aUsed [".print_r($aUsed, true)."]\n";

        return $aUsed;
    }
// end of GetUsedDataElement

    function Parse()
    {
        $isoFormat = new ISO8583Format;
        $aISODataElement = $isoFormat->getFormat();
        // var_dump($aISODataElement);

        $bParsed = false;
        $nStreamLen = strlen($this->sISOMsgStream);
        if ($nStreamLen >= 20) { // minimal length of shortest ISO8583 message stream (MTI + PRIMARY BITMAP)
            // determine ISO8583 version
            $sMTIVersion = substr($this->sISOMsgStream, 0, 1);
            switch ($sMTIVersion) {
                case "0": $this->sISOVersion = "1987";
                    break;
                case "1": $this->sISOVersion = "1993";
                    break;
                case "2": $this->sISOVersion = "2003";
                    break;
            }
            //echo "[INFO] sISOVersion [".$this->sISOVersion."]\n";

            if (@isset($aISODataElement[$this->sISOVersion])) { // there is data element definition of parsed ISO8583 message stream
                //echo "[INFO] here\n";
                $this->aISODataElements = array();
                // get MTI
                $this->sMTI = substr($this->sISOMsgStream, 0, 4);
                $this->aISODataElements[0] = $this->sMTI;
                // get bit map
                $this->sPrimaryBitMap = substr($this->sISOMsgStream, 4, 16);
                $this->aISODataElements[1] = $this->sPrimaryBitMap;

                //echo "[INFO] this->aISODataElements [".print_r($this->aISODataElements, true)."]\n";
                $aUsedPrimaryDataElement = array();
                $aUsedPrimaryDataElement = $this->GetUsedDataElement($this->sPrimaryBitMap, 0); // 0 = check primary bitmap
                $nPrimaryDELen = sizeof($aUsedPrimaryDataElement);
                $nSecondaryDELen = 0;
                $nTertiaryDELen = 0;

                //echo "[INFO] aUsedPrimaryDataElement [".print_r($aUsedPrimaryDataElement, true)."]\n";

                if ($nPrimaryDELen > 0) {
                    $iPrimaryStart = 0;
                    $iSecondaryStart = 0;
                    $iTertiaryStart = 0;

                    $iDEStartIdx = 4 + 16;

                    // update bitmap if there is secondary bitmap is defined
                    if ($aUsedPrimaryDataElement[0] == 1) { // de #1 (first bit of primary bitmap) = 1 => also use secondary bitmap
                        $iPrimaryStart = 1;

                        $this->sSecondaryBitMap = substr($this->sISOMsgStream, 20, 16);
                        $this->aISODataElements[1] .= $this->sSecondaryBitMap;

                        $aUsedSecondaryDataElement = array();
                        $aUsedSecondaryDataElement = $this->GetUsedDataElement($this->sSecondaryBitMap, 1); // 1 = check secondary bitmap

                        $nSecondaryDELen = sizeof($aUsedSecondaryDataElement);
                        if ($nSecondaryDELen > 0) {
                            $iDEStartIdx += 16;

                            // update bitmap if there is tertiary bitmap is defined
                            if ($aUsedSecondaryDataElement[0] == 65) { // de #1 (first bit of secondary bitmap) = 1 => also use tertiary bitmap
                                $iSecondaryStart = 1;

                                /* $this->sTertiaryBitMap = substr($this->sISOMsgStream, 36, 16);
                                  $this->aISODataElements[1] .= $this->sTertiaryBitMap;

                                  $aUsedTertiaryDataElement = array();
                                  $aUsedTertiaryDataElement = $this->GetUsedDataElement($this->sTertiaryBitMap, 2, $aUsedTertiaryDataElement); // 2 = check tertiary bitmap

                                  $nTertiaryDELen = sizeof($aUsedTertiaryDataElement);
                                  if ($nTertiaryDELen > 0)
                                  {
                                  $iDEStartIdx += 16;
                                  } */
                            }
                        }
                    }

                    // process primary data element
                    if ($nPrimaryDELen > 0) {
                        for ($i = $iPrimaryStart; $i < $nPrimaryDELen; $i++) {
                            $sFieldType = $aISODataElement[$this->sISOVersion][$aUsedPrimaryDataElement[$i]]["format"];
                            $nFieldLen = 0;
                            if ($sFieldType == "LLVAR") { // LLVAR
                                $nVarLen = 2;
                                $nFieldLen = intval(substr($this->sISOMsgStream, $iDEStartIdx, $nVarLen));
                                $iDEStartIdx += $nVarLen;
                            } else if ($sFieldType == "LLLVAR") { // LLLVAR
                                $nVarLen = 3;
                                $nFieldLen = intval(substr($this->sISOMsgStream, $iDEStartIdx, $nVarLen));
                                $iDEStartIdx += $nVarLen;
                            } else if ($sFieldType == "LLLLVAR") { // LLLLVAR
                                $nVarLen = 4;
                                $nFieldLen = intval(substr($this->sISOMsgStream, $iDEStartIdx, $nVarLen));
                                $iDEStartIdx += $nVarLen;
                            } else { // fixed length
                                $nFieldLen = $aISODataElement[$this->sISOVersion][$aUsedPrimaryDataElement[$i]]["len"];
                            }

                            //echo "[INFO] >> i [$i] aUsedPrimaryDataElement[i] [".$aUsedPrimaryDataElement[$i]."] iDEStartIdx [$iDEStartIdx] nFieldLen [$nFieldLen]\n";
                            $this->aISODataElements[$aUsedPrimaryDataElement[$i]] = substr($this->sISOMsgStream, $iDEStartIdx, $nFieldLen);
                            $iDEStartIdx += $nFieldLen; // next element index
                        }
                    }

                    // process secondary data element
                    if ($nSecondaryDELen > 0) {
                        for ($i = $iSecondaryStart; $i < $nSecondaryDELen; $i++) {
                            $sFieldType = $aISODataElement[$this->sISOVersion][$aUsedSecondaryDataElement[$i]]["format"];
                            $nFieldLen = 0;
                            if ($sFieldType == "LLVAR") { // LLVAR
                                $nVarLen = 2;
                                $nFieldLen = intval(substr($this->sISOMsgStream, $iDEStartIdx, $nVarLen));
                                $iDEStartIdx += $nVarLen;
                            } else if ($sFieldType == "LLLVAR") { // LLLVAR
                                $nVarLen = 3;
                                $nFieldLen = intval(substr($this->sISOMsgStream, $iDEStartIdx, $nVarLen));
                                $iDEStartIdx += $nVarLen;
                            } else if ($sFieldType == "LLLLVAR") { // LLLLVAR
                                $nVarLen = 4;
                                $nFieldLen = intval(substr($this->sISOMsgStream, $iDEStartIdx, $nVarLen));
                                $iDEStartIdx += $nVarLen;
                            } else { // fixed length
                                $nFieldLen = $aISODataElement[$this->sISOVersion][$aUsedSecondaryDataElement[$i]]["len"];
                            }

                            //echo "[INFO] >> i [$i] aUsedSecondaryDataElement[i] [".$aUsedSecondaryDataElement[$i]."] iDEStartIdx [$iDEStartIdx] nFieldLen [$nFieldLen]\n";
                            $this->aISODataElements[$aUsedSecondaryDataElement[$i]] = substr($this->sISOMsgStream, $iDEStartIdx, $nFieldLen);
                            $iDEStartIdx += $nFieldLen; // next element index
                        }
                    }

                    // process primary data element
                    if ($nTertiaryDELen > 0) {
                        for ($i = $iTertiaryStart; $i < $nTertiaryDELen; $i++) {
                            $sFieldType = $aISODataElement[$this->sISOVersion][$aUsedTertiaryDataElement[$i]]["format"];
                            $nFieldLen = 0;
                            if ($sFieldType == "LLVAR") { // LLVAR
                                $nVarLen = 2;
                                $nFieldLen = intval(substr($this->sISOMsgStream, $iDEStartIdx, $nVarLen));
                                $iDEStartIdx += $nVarLen;
                            } else if ($sFieldType == "LLLVAR") { // LLLVAR
                                $nVarLen = 3;
                                $nFieldLen = intval(substr($this->sISOMsgStream, $iDEStartIdx, $nVarLen));
                                $iDEStartIdx += $nVarLen;
                            } else if ($sFieldType == "LLLLVAR") { // LLLLVAR
                                $nVarLen = 4;
                                $nFieldLen = intval(substr($this->sISOMsgStream, $iDEStartIdx, $nVarLen));
                                $iDEStartIdx += $nVarLen;
                            } else { // fixed length
                                $nFieldLen = $aISODataElement[$this->sISOVersion][$aUsedTertiaryDataElement[$i]]["len"];
                            }

                            //echo "[INFO] >> i [$i] aUsedTertiaryDataElement[i] [".$aUsedTertiaryDataElement[$i]."] iDEStartIdx [$iDEStartIdx] nFieldLen [$nFieldLen]\n";
                            $this->aISODataElements[$aUsedTertiaryDataElement[$i]] = substr($this->sISOMsgStream, $iDEStartIdx, $nFieldLen);
                            $iDEStartIdx += $nFieldLen; // next element index
                        }
                    }

                    $bParsed = true;
                } else {
                    // no data elements are defined -> shortest ISO8583 message (MTI + PRIMARY BITMAP ONLY)
                    // so, NOTHING TO DO
                    $bParsed = true;
                }
            } else {
                $this->iErrCode = -101;
                $this->sErrMsg = "Undefined ISO8583:" . $this->sISOVersion . " data element definition";
                //echo "[ERROR] ".$this->sErrMsg."\n";
            }
        } else {
            $this->iErrCode = -102;
            $this->sErrMsg = "length is less than minimal (20)";
            //echo "[ERROR] ".$this->sErrMsg."\n";
        }

        return $bParsed;
    }
// end of Parse

    function GetParsedDataElement()
    {
        return $this->aISODataElements;
    }
// end of GetParsedDataElement

    // LAST ERROR
    function GetLastErrorCode()
    {
        return $this->iErrCode;
    }
// end of GetLastErrorCode

    function GetLastErrorMsg()
    {
        return $this->sErrMsg;
    }
// end of GetLastErrorMsg
}

// end of CISO8583Parser

class CISO8583Message
{

    // constants

    const SUPPORTED_VERSION = "1987;1993;2003";

    // internal vars
    private $aDataElement = array();
    private $sVersion = "2003";
    private $sConstructedStream = "";
    // debug
    private $bDebug = false;
    private $sLogFileName = "";
    // last error
    private $iErrCode = 0;
    private $sErrMsg = "";

    function __construct()
    {
        
    }
// end of __construct

    function SetVersion($sVersion = "2003")
    {
        $this->sVersion = $sVersion;
        if (strpos(self::SUPPORTED_VERSION, $this->sVersion) === FALSE)
            $this->sVersion = "2003";
    }
// end of SetVersion

    function SetValueForDataElement($iIdx = 2, $sValue = "")
    {
        $this->aDataElement[$iIdx] = $sValue;
    }
// end of SetValueForDataElement

    function GetValueForDataElement($iIdx = 0)
    {
        $Val = NULL;
        if (@isset($this->aDataElement[$iIdx])) {
            $Val = $this->aDataElement[$iIdx];
        }
        return $Val;
    }
// end of GetValueForDataElement

    function GetProperVersionBit()
    {
        $sBit = "2"; // 2003

        switch ($this->sVersion) {
            case "1987": $sBit = "0";
                break;
            case "1993": $sBit = "1";
                break;
        }

        return $sBit;
    }
// end of GetProperVersionBit

    function GenerateBitMap()
    {
        $aBitMap = array();
        $aBitMap[0] = "";
        $aBitMap[1] = "";
        $aBitMap[2] = "";

        $iPrev = 1;
        foreach ($this->aDataElement as $key => $val) {

            if ($key > 1) {
                if ($key != 65) {
                    $iBitMapIdx = ($key < 65 ? 0 : ($key > 128 ? 2 : 1)); // if $keyidx < 65 masuk primary bitmap, 65-126 secondari > 128 tertiary

                    if ($iBitMapIdx == 1) {
                        if ($iPrev <= 64)
                            $iPrev = 65;
                    } else if ($iBitMapIdx == 2) {
                        if ($iPrev <= 128)
                            $iPrev = 129;
                    }

                    $n = $key - $iPrev - 1;
                    // echo "iBitMapIdx: $iBitMapIdx DE:$key  DV:$val  iPrev:$iPrev n:$n<br>";
                    for ($i = 0; $i < $n; $i++) {
                        $aBitMap[$iBitMapIdx] .= "0";
                    }
                    $iPrev = $key;

                    $aBitMap[$iBitMapIdx] .= "1";
                }
            }
        }

        // check bitmap existence & update related bitmap
        $sDEEmpty = str_pad("", "64", "0", STR_PAD_RIGHT);

        $aBitMap[2] = "0" . $aBitMap[2];
        $aBitMap[2] = str_pad($aBitMap[2], 64, "0", STR_PAD_RIGHT);
        if ($aBitMap[2] != $sDEEmpty) {
            $aBitMap[1] = "1" . $aBitMap[1];
        } else {
            $aBitMap[2] = "";
            $aBitMap[1] = "0" . $aBitMap[1];
        }

        $aBitMap[1] = str_pad($aBitMap[1], 64, "0", STR_PAD_RIGHT);
        if ($aBitMap[1] != $sDEEmpty) {
            $aBitMap[0] = "1" . $aBitMap[0];
        } else {
            $aBitMap[1] = "";
            $aBitMap[0] = "0" . $aBitMap[0];
        }

        //echo "[DEBUG] bitmap bin [".print_r($aBitMap, true)."]\n";
        // var_dump($aBitMap);
        for ($i = 0; $i < 3; $i++) {
            if ($aBitMap[$i] != "")
                $aBitMap[$i] = str_pad($aBitMap[$i], 64, "0", STR_PAD_RIGHT);
            //echo "[DEBUG] >> bitmap bin $i [".$aBitMap[$i]."] [".strlen($aBitMap[$i])."]\n";
            $Converter = new CBaseConverter;
            $aBitMap[$i] = $Converter->_ascii_bin_to_hexa($aBitMap[$i]);
        }

        //echo "[DEBUG] bitmap hexa [".print_r($aBitMap, true)."]\n";

        return $aBitMap;
    }
// end of GenerateBitMap

    function ConstructStream()
    {
        $isoFormat = new ISO8583Format;
        $aISODataElement = $isoFormat->getFormat();
        // global $aISODataElement;
        $bOK = false;

        if (@ksort($this->aDataElement)) {
            // array aDataElement is sorted by its index
            // construct stream
            $aBitMap = $this->GenerateBitMap();
            $sMTI = (@isset($this->aDataElement[0]) ? sprintf("%04s", substr($this->aDataElement[0], 0, 4)) : NULL);
            if ($sMTI != NULL) {
                if (strlen($sMTI) > 3)
                    $sMTI = substr($sMTI, 1, 3);
            }

            if ($sMTI != NULL) {
                $sStream = $sMTI . $aBitMap[0] . ($aBitMap[1] != "" ? $aBitMap[1] : "");

                // construct data element
                foreach ($this->aDataElement as $key => $val) {
                    if (($key != 65) && ($key >= 2) && ($key <= 128)) { // 65 is tertiary bitmap
                        // determine padding character and orientation
                        $sType = $aISODataElement[$this->sVersion][$key]["type"];
                        $cPadChar = " ";
                        $bLeftPadding = false;
                        if (strtolower($sType) == "n") {
                            $cPadChar = "0";
                            $bLeftPadding = true;
                        }

                        // validate value
                        $sValue = (@isset($val) ? $val : "");
                        $sValue = @substr($sValue, 0, $aISODataElement[$this->sVersion][$key]["len"]); // ensure assigned value is not longer than its maximum length
                        // check format
                        $bFixedLength = false;
                        $sFormat = strtoupper($aISODataElement[$this->sVersion][$key]["format"]);
                        if ($sFormat == "LLLLVAR")
                            $sStream .= sprintf("%04d", strlen($sValue));
                        else if ($sFormat == "LLLVAR")
                            $sStream .= sprintf("%03d", strlen($sValue));
                        else if ($sFormat == "LLVAR")
                            $sStream .= sprintf("%02d", strlen($sValue));
                        else
                            $bFixedLength = true;

                        // append value to stream
                        if ($bFixedLength)
                            $sStream .= str_pad($sValue, $aISODataElement[$this->sVersion][$key]["len"], $cPadChar, ($bLeftPadding ? STR_PAD_LEFT : STR_PAD_RIGHT));
                        else
                            $sStream .= $sValue;
                    } else { // 65 = tertiary bitmap
                        $sStream .= $aBitMap[2];
                    }
                }

                $sStream = $this->GetProperVersionBit() . $sStream;
                $this->sConstructedStream = $sStream;

                $bOK = true;
            } else {
                $this->iErrCode = -011;
                $this->sErrMsg = "MTI is not defined";
            }
        } else {
            $this->iErrCode = -001;
            $this->sErrMsg = "Unable to sort internal arrays";
        }

        return $bOK;
    }
// end of ConstructStream

    function GetConstructedStream()
    {
        return $this->sConstructedStream;
    }
// end of GetConstructedStream

    // LAST ERROR
    function GetLastErrorCode()
    {
        return $this->iErrCode;
    }
// end of GetLastErrorCode

    function GetLastErrorMsg()
    {
        return $this->sErrMsg;
    }
// end of GetLastErrorMsg
}

// end of CISO8583Message
// DRIVER
$iTimeStart = microtime(true);

//print_r($aISODataElement);
// ----------------------
// DRIVER CISO8583Message
// ----------------------
/*
  $sStream = "";
  $m = new CISO8583Message();
  $m->SetValueForDataElement(0, "1410");
  $m->SetValueForDataElement(2, "53501");
  $m->SetValueForDataElement(39, "00112"); // will be truncated to max length (max = 4 char)
  $m->SetValueForDataElement(48, "10000V3");
  $m->SetValueForDataElement(41, "1234567890123456");
  $m->SetValueForDataElement(68, "999999999");
  $m->SetValueForDataElement(128, "8888");
  $m->SetValueForDataElement(64, "4444");
  $m->SetValueForDataElement(32, "2222");
  //$m->SetVersion("1987");
  date_default_timezone_set("Asia/Jakarta");
  $m->SetValueForDataElement(12, strftime("%Y%m%d%H%M%S", time()));
  //$m->SetVersion("1987");
  if ($m->ConstructStream())
  {
  $sStream = $m->GetConstructedStream();
  echo "[INFO] Stream [$sStream] [".strlen($sStream)."]\n";
  }
  else
  {
  echo "[ERROR] Unable to construct message. (".sprintf("#%d:%s", $m->GetLastErrorCode(), $m->GetLastErrorMsg()).")\n";
  }

  // ---------------------
  // DRIVER CISO8583Parser
  // ---------------------
  $sStream = $sStream;
  //$sStream = "280000100000010100002008050207230000200710000D3";
  //$sStream = "2810001000000301000020080502072300000000100710000D3";
  //$sStream = "21004030004100010000055350110000000000120080502072300601507011000001910000D3530000000006";
  //$sStream = "2110503000410201000005535013600000000100000100000000001200805020723006015070110000000023210000D3530000000001101E5BA37AC52083904FDEF185BE297009ASUBSCRIBER NAME          53585021-3754678    R1  000000450000000000200804200804202008032100000100000D00000000000000000000000000000001745000017550000000000000000000000000000000000";
  //$sStream = "220050300041000100000553501360000000010000010000000000120080502072300601507011000026510000D35300000000011101E5BA37AC52083904FDEF185BE297009A874CC544BF2663E46D738268518F8F62SUBSCRIBER NAME          53585021-3754678    R1  000000450000000000200804200804202008032100000100000D00000000000000000000000000000001745000017550000000000000000000000000000000000";
  //$sStream = "221050320041020100000553501360000000010000010000000000120080502072300200805026015070110000000026510000D35300000000011101E5BA37AC52083904FDEF185BE297009A874CC544BF2663E46D738268518F8F62SUBSCRIBER NAME          53585021-3754678    R1  000000450000000000200804200804202008032100000100000D00000000000000000000000000000001745000017550000000000000000000000000000000000";
  //$sStream = "240050300041000101000553501360000000010000010000000000120080502072300601507011000026510000D35300000000011101E5BA37AC52083904FDEF185BE297009A874CC544BF2663E46D738268518F8F62SUBSCRIBER NAME          53585021-3754678    R1  000000450000000000200804200804202008032100000100000D00000000000000000000000000000001745000017550000000000000000000000000000000000372200100000000001200805020723000110000";
  //$sStream = "2410503000410201010005535013600000000100000100000000001200805020723006015070110000000026510000D35300000000011101E5BA37AC52083904FDEF185BE297009A874CC544BF2663E46D738268518F8F62SUBSCRIBER NAME          53585021-3754678    R1  000000450000000000200804200804202008032100000100000D00000000000000000000000000000001745000017550000000000000000000000000000000000372200100000000001200805020723000110000";
  $o = new CISO8583Parser($sStream);
  if ($o->Parse())
  {
  $a = $o->GetParsedDataElement();
  echo "<br>[INFO] RESULT:\n";
  print_r($a);

  // check bitmap
  if (@isset($a[1]))
  echo "<br>ascii binary bitmap [".CBaseConverter::_ascii_hexa_to_bin($a[1])."]\n";
  }
  else
  {
  echo "[ERROR] Unable to parse message stream. It might not a valid ISO8583 message stream. (".sprintf("#%d:%s", $o->GetLastErrorCode(), $o->GetLastErrorMsg()).")\n";
  }

  // TIMER

  $iTimeEnd = microtime(true);
  $iTimeExec = $iTimeEnd - $iTimeStart;

  echo sprintf("<br>Executed in %.8f s = %.8f ms\n", $iTimeExec, $iTimeExec * 1000); */

?>
