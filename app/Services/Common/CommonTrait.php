<?php

namespace App\Services\Common;

use Illuminate\Support\Facades\Log;

trait CommonTrait
{
    private $cConfigUrlJasaTarif = 'settlement.url.jasatarif';
    
    private function traceLog($pMessage)
    {
        Log::debug($pMessage);
    }

    private function sendAPI($pUrl, $pData)
    {
        $tStringData = json_encode($pData);
        $this->traceLog("API Request to {$pUrl} : {$tStringData}");

        $tMethod = 'POST';
        $requestBody = str_replace(array(" ", "\n", "\t", "\r"), array("", "", "", ""), $tStringData);
        $tCurl = curl_init($pUrl);
        curl_setopt($tCurl, CURLOPT_CUSTOMREQUEST, $tMethod);
        curl_setopt($tCurl, CURLOPT_POSTFIELDS, $tStringData);
        curl_setopt($tCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $tCurl,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($tStringData),
            )
        );
        sleep(0.5);
        $tResult = curl_exec($tCurl);
        curl_close($tCurl);

        $this->traceLog("API Response from {$pUrl} : {$tResult}");

        return json_decode($tResult, true);
    }

    private function sendSocket($address, $port, $out)
    {
        $this->traceLog("[sendSocket] start with address {$address}, port {$port}, and out {$out}");

        $timeout = 1000;
        $s = '';
        $bTimeout = 0;

        $fp = fsockopen($address, $port, $errno, $errstr, $timeout);

        if (!$fp) {
            $bTimeout = 999;
        } else {
            //$n = fwrite($fp, GetLengthByte(strlen($out)), 2); //byte order
            $n = fwrite($fp, $out, strlen($out));
            $n = fwrite($fp, chr(-1));
            @stream_set_timeout($fp, $timeout);

            $c = '';
            $bDone = false;
            $bHead = false;
            $lenCount = 0;
            $i = 0;
            while ((!feof($fp)) && ($bTimeout == 0) && (!$bDone)) {
                $info = @stream_get_meta_data($fp);
                if ($info['timed_out']) {
                    $bTimeout = 1;
                }

                if ($bTimeout == 0) {
                    $c = fread($fp, 1);
                    if ($c != chr(-1)) {
                        $s .= $c;
                    } else {
                        $bDone = true;
                    }
                } // end of !$bTimeout
            }

            fclose($fp);
        }
        $sResp = $s;

        $this->traceLog("[sendSocket] end with address {$address}, port {$port}, out {$out}, and result {$sResp}");
        return $sResp;
    }

    private function getJasaTarif($pSofId, $pMerchantId, $pPaymentMethodId, $pBiayaProduk, $pBiayaLayanan = '0')
    {
        $tUrlJasaTarif = config($this->cConfigUrlJasaTarif, 'http://10.168.26.10:13235/getTarif');
        $tData = [
            'sof_id' => $pSofId,
            'merchant_id' => $pMerchantId,
            'payment_method_id' => $pPaymentMethodId,
            'biaya_produk' => $pBiayaProduk,
            'biaya_layanan' => $pBiayaLayanan,
        ];
        $tApiResult = $this->sendAPI($tUrlJasaTarif, $tData);
        return collect($tApiResult);
    }
}
