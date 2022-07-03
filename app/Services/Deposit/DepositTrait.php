<?php

namespace App\Services\Deposit;

use App\Services\Common\CommonTrait;
use Exception;

trait DepositTrait
{
    use CommonTrait;

    private $cConfigDepositHost = 'settlement.deposit.host';
    private $cConfigDepositPort = 'settlement.deposit.port';
    private $cConfigDepositIsactive = 'settlement.deposit.isactive';

    private function constructDepositDebitRequest($pAccountNumber, $pAmount, $pRefnum, $pRemark): string
    {
        $tRequest = new DepositRequest();
        $tRequest->SetComponentTmp('mti', DepositRequest::$cMtiRequest);
        $tRequest->SetComponentTmp('account_number', $pAccountNumber);
        $tRequest->SetComponentTmp('processing_code', DepositRequest::$cPcDebitRequest);
        $tRequest->SetComponentTmp('amount', '3600');
        $tRequest->SetComponentTmp('datetime', now()->format('YYYYMMDDHHmmss'));
        $tRequest->SetComponentTmp('priv', [
            'amount' => $pAmount,
            'sender' => '0',
            'refnum' => $pRefnum,
        ]);
        $tRequest->SetComponentTmp('trx_code', $pRefnum);
        $tRequest->SetComponentTmp('remark', $pRemark);
        $tRequest->SetComponentTmp('req_id', $pRefnum);

        $tRequest->ConstructStream();
        return $tRequest->GetConstructedStream();
    }

    private function constructDepositCreditRequest($pAccountNumber, $pAmount, $pRefnum, $pRemark): string
    {
        $tRequest = new DepositRequest();
        $tRequest->SetComponentTmp('mti', DepositRequest::$cMtiRequest);
        $tRequest->SetComponentTmp('account_number', $pAccountNumber);
        $tRequest->SetComponentTmp('processing_code', DepositRequest::$cPcCreditRequest);
        $tRequest->SetComponentTmp('amount', '3600');
        $tRequest->SetComponentTmp('datetime', now()->format('YYYYMMDDHHmmss'));
        $tRequest->SetComponentTmp('priv', [
            'amount' => $pAmount,
            'sender' => '0',
            'refnum' => $pRefnum,
        ]);
        $tRequest->SetComponentTmp('trx_code', $pRefnum);
        $tRequest->SetComponentTmp('remark', $pRemark);
        $tRequest->SetComponentTmp('req_id', $pRefnum);

        $tRequest->ConstructStream();
        return $tRequest->GetConstructedStream();
    }

    private function isDepositResponseSuccess($pStream): bool
    {
        $tResult = new DepositResponse($pStream);
        $tResult->ExtractDataElement();
        $tDataElement = $tResult->dataElement;
        $tRc = $tDataElement['rc'];
        return $tRc == '0000';
    }

    private function sendCreditRequest($pAccountNumber, $pAmount, $pRefnum, $pRemark)
    {
        $this->traceLog("[sendCreditRequest] start with account_number {$pAccountNumber}, amount {$pAmount}, refnum {$pRefnum}, and remark {$pRemark}");

        if (filter_var(config($this->cConfigDepositIsactive, 'false'), FILTER_VALIDATE_BOOLEAN)) {
            $tRequest = $this->constructDepositCreditRequest($pAccountNumber, $pAmount, $pRefnum, $pRemark);
            $tDepositHost = config('settlement.deposit_host');
            $tDepositPort = config('settlement.deposit_port');
            $tRawResponse = $this->sendSocket($tDepositHost, $tDepositPort, $tRequest);
            if (!$this->isDepositResponseSuccess($tRawResponse)) {
                throw new Exception('Credit request is failed.');
            }
        } else {
            $this->traceLog("[sendCreditRequest] config isactive is set to false -> skip sendCreditRequest.");
        }

        $this->traceLog("[sendCreditRequest] end with account_number {$pAccountNumber}, amount {$pAmount}, refnum {$pRefnum}, and remark {$pRemark}");
    }

    private function sendDebitRequest($pAccountNumber, $pAmount, $pRefnum, $pRemark)
    {
        $this->traceLog("[sendDebitRequest] start with account_number {$pAccountNumber}, amount {$pAmount}, refnum {$pRefnum}, and remark {$pRemark}");

        if (filter_var(config($this->cConfigDepositIsactive, 'false'), FILTER_VALIDATE_BOOLEAN)) {
            $tRequest = $this->constructDepositDebitRequest($pAccountNumber, $pAmount, $pRefnum, $pRemark);
            $tDepositHost = config($this->cConfigDepositHost, '10.168.26.10');
            $tDepositPort = config($this->cConfigDepositPort, '13130');
            $tRawResponse = $this->sendSocket($tDepositHost, $tDepositPort, $tRequest);
            if (!$this->isDepositResponseSuccess($tRawResponse)) {
                throw new Exception('Debit request is failed.');
            }
        } else {
            $this->traceLog("[sendCreditRequest] config isactive is set to false -> skip sendDebitRequest.");
        }

        $this->traceLog("[sendDebitRequest] end with account_number {$pAccountNumber}, amount {$pAmount}, refnum {$pRefnum}, and remark {$pRemark}");
    }
}
