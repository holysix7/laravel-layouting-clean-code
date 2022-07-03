<?php
namespace App\Http\Traits\ISO8583;

use App\Http\Traits\ISO8583\CISO8583Message;

class DepositRequest extends CISO8583Message
{
    public static $cMtiRequest = '2200';
    public static $cPcDebitRequest = '010000';
    public static $cPcCreditRequest = '210000';

    function __construct()
    {
        $this->SetVersion("2003");
        $this->SetValueForDataElement(0, "2200");
        $this->SetValueForDataElement(12, now()->format('YYYYMMDDHHmmss'));
    }

    private function GetMappingKeyIdx($sKey)
    {
        switch ($sKey) {
            case 'mti' : $iKey = 0;
                break;
            case 'account_number' : $iKey = 2;
                break;
            case 'processing_code' : $iKey = 3;
                break;
            case 'amount' : $iKey = 4;
                break;
            case 'datetime' : $iKey = 12;
                break;
            case 'priv' : $iKey = 48;
                break;
            case 'trx_code' : $iKey = 60;
                break;
            case 'remark' : $iKey = 61;
                break;
            case 'req_id' : $iKey = 62;
                break;
        }
        return $iKey;
    }

    private function ConstructPrivateData($aPriv)
    {
        $sPriv = '';
        $sPriv .= str_pad($aPriv['amount'], 32, "0", STR_PAD_LEFT);
        $sPriv .= str_pad($aPriv['sender'], 10, " ", STR_PAD_RIGHT);
        $sPriv .= str_pad($aPriv['refnum'], 32, " ", STR_PAD_RIGHT);

        return $sPriv;
    }

    public function SetComponentTmp($sKey, $value)
    {
        $keyIdx = $this->GetMappingKeyIdx($sKey);
        switch ($sKey) {
            case 'priv' : $value = $this->ConstructPrivateData($value);
                break;
            case 'amount' : $value = str_pad($value, 16, "0", STR_PAD_RIGHT);
                break;
            case 'remark' : $value = str_pad($value, 100, " ", STR_PAD_RIGHT);
                break;
            case 'req_id' : $value = str_pad($value, 32, " ", STR_PAD_RIGHT);
                break;
            default:
        }
        $this->SetValueForDataElement($keyIdx, $value);
    }
}

?>