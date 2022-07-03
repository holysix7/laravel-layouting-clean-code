<?php

namespace App\Http\Traits\ISO8583;

trait DepositTrait
{
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
}
