<?php
namespace App\Http\Traits\ISO8583;

use App\Http\Traits\ISO8583\CISO8583Parser;
use App\Http\Traits\ISO8583\ProtocolGeneric;

class DepositResponse extends CISO8583Parser
{
    public $dataElement = array();
    public $privateData = array();
    public $privateDataSingle = array();
    public $detailData = array();
    public $trxData = array();

    function __construct($isoStream)
    {
        parent::__construct($isoStream);
    }

    private function GetMappingValue($idx)
    {
        switch ($idx) {
            case 0: $sKey = 'mti';
                break;
            case 1: $sKey = 'bitmap';
                break;
            case 2: $sKey = 'pan';
                break;
            case 3: $sKey = 'pc';
                break;
            case 4: $sKey = 'amount';
                break;
            case 11: $sKey = 'stan';
                break;
            case 12: $sKey = 'dt';
                break;
            case 15: $sKey = 'settledt';
                break;
            case 26: $sKey = 'merchant';
                break;
            case 32: $sKey = 'bankcode';
                break;
            case 33: $sKey = 'cid';
                break;
            case 39: $sKey = 'rc';
                break;
            case 41: $sKey = 'ppid';
                break; 
            case 48: $sKey = 'priv';
                break;
            case 56: $sKey = 'ode';
                break; 
            case 60: $sKey = 'trx_code';
                break;
            case 61: $sKey = 'feedback';
                break;
            case 62: $sKey = 'dtldt';
                break;
            case 63: $sKey = 'infotext';
                break;
            case 120: $sKey = 'trxid';
                break;
        }
        return $sKey;
    }

    public function ExtractDataElement()
    {
        if ($this->Parse()) {
            $rDataElmt = $this->GetParsedDataElement();
            foreach ($rDataElmt as $iKey => $value) {
                $sKey = $this->GetMappingValue($iKey);
                $this->dataElement[$sKey] = $value;
            }
        }
    }
}

?>