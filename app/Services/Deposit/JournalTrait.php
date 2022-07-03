<?php

namespace App\Services\Deposit;

use App\QueryService\QueryService;

trait JournalTrait
{
    use DepositTrait;

    private $cConfigQsSelDepAccountNumber = 'settlement.qs.sel_dep_account_number';
    private $cConfigTemplateRemarkPayment = 'settlement.template.remark.payment';
    private $cConfigTemplateRemarkReversal = 'settlement.template.remark.reversal';

    private function saveJournalAdjustment($pRecon, $pReconItem, $pTransaction)
    {
        $this->traceLog('[saveJournalAdjustment] start with recon ' . json_encode($pRecon) 
            . ', recon_item ' . json_encode($pReconItem) . ', and transaction ' . json_encode($pTransaction));

        $tSofId = $pRecon['sof_id'];
        $tPayMerchantId = $pTransaction['merchant_id'];
        $tPaymentMethodId = $pTransaction['payment_method_id'];
        $tAmount = $pReconItem['sof_amount'];
        $tRefnum = $pReconItem['sof_refnum'];
        $tRemark = $this->constructRemarkPayment($tRefnum);

        $tBiayaLayanan = $pTransaction['fee_transfer'];
        $tBiayaProduk = $tAmount - $tBiayaLayanan;
        $tJasaTarif = $this->getJasaTarif($tSofId, $tPayMerchantId, $tPaymentMethodId, $tBiayaProduk, $tBiayaLayanan);
        $tPembagianJasaTarif = $tJasaTarif['detail'];

        // adjustment pendapatan merchant
        $tAccountNumber = $this->getCreditAccountNumber($this->cAccountTypeMerchant, $tPayMerchantId);
        $this->sendCreditRequest($tAccountNumber, $tAmount, $tRefnum, $tRemark);

        // // adjustment pendapatan ditahan
        $tAccountNumber = $this->getDebitAccountNumber($this->cAccountTypeDitahan);
        $this->sendDebitRequest($tAccountNumber, $tAmount, $tRefnum, $tRemark);

        // adjustment pendapatan global
        $tAccountNumber = $this->getDebitAccountNumber($this->cAccountTypeGlobal);
        $this->sendDebitRequest($tAccountNumber, $tPembagianJasaTarif['sof'], $tRefnum, $tRemark);

        $this->traceLog('[saveJournalAdjustment] end with recon ' . json_encode($pRecon) 
            . ', recon_item ' . json_encode($pReconItem) . ', and transaction ' . json_encode($pTransaction));
    }

    private function saveJournalReversal($pReconItem)
    {
        $this->traceLog('[saveJournalReversal] start with recon_item_id ' . json_encode($pReconItem));

        $tSofId = $this->mRecon['sof_id'];
        $tPayMerchantId = $pReconItem['merchant_id'];
        $tPaymentMethodId = $pReconItem['payment_method_id'];
        $tAmount = $pReconItem['pg_amount'];
        $tRefnum = $pReconItem['pg_refnum'];
        $tRemark = $this->constructRemarkReversal($tRefnum);

        $tBiayaProduk = $tAmount;
        $tBiayaLayanan = $pReconItem['fee_transfer'];
        $tJasaTarif = $this->getJasaTarif($tSofId, $tPayMerchantId, $tPaymentMethodId, $tBiayaProduk, $tBiayaLayanan);
        $tPembagianJasaTarif = $tJasaTarif['detail'];
        $tParentMerchantId = $tJasaTarif['is_submerchant'] ? $tJasaTarif['parent_id'] : $tPayMerchantId;
        $tSubMerchantId = $tJasaTarif['is_submerchant'] ? $tPayMerchantId : null;

        // adjustment pendapatan global
        $tAccountNumber = $this->getDebitAccountNumber($this->cAccountTypeGlobal);
        $this->sendDebitRequest($tAccountNumber, $tAmount, $tRefnum, $tRemark);

        // adjustment rekening penampungan
        $tAccountNumber = $this->getDebitAccountNumber($this->cAccountTypePenampungan);
        $this->sendDebitRequest($tAccountNumber, $tAmount, $tRefnum, $tRemark);

        // adjustment PG
        $tAccountNumber = $this->getDebitAccountNumber($this->cAccountTypePg);
        $this->sendDebitRequest($tAccountNumber, $tPembagianJasaTarif['pg'], $tRefnum, $tRemark);

        // adjustment SOF
        $tAccountNumber = $this->getDebitAccountNumber($this->cAccountTypeSof, null, $tSofId);
        $this->sendDebitRequest($tAccountNumber, $tPembagianJasaTarif['sof'], $tRefnum, $tRemark);

        // adjustment merchant
        $tAccountNumber = $this->getDebitAccountNumber($this->cAccountTypeMerchant, $tParentMerchantId);
        $this->sendDebitRequest($tAccountNumber, $tPembagianJasaTarif['merchant'], $tRefnum, $tRemark);

        if ($tJasaTarif['is_submerchant']) {
            // adjustment submerchant
            $tAccountNumber = $this->getDebitAccountNumber($this->cAccountTypeSubmerchant, $tSubMerchantId);
            $this->sendDebitRequest($tAccountNumber, $tPembagianJasaTarif['submerchant'], $tRefnum, $tRemark);
        }

        $this->traceLog('[saveJournalReversal] end with recon_item_id ' . json_encode($pReconItem));
    }

    public function getCreditAccountNumber($pType, $pMerchantId = null, $pSofId = null)
    {
        $this->traceLog("[getCreditAccountNumber] start with type {$pType}, merchant_id {$pMerchantId}, and sof_id {$pSofId}");

        $tParam = [
            'acc_type' => $pType,
            'acc_prefix' => '4',
            'merchant_id' => $pMerchantId != null ? $pMerchantId : '',
            'sof_id' => $pSofId != null ? $pSofId : '',
        ];
        $tFuncName = config($this->cConfigQsSelDepAccountNumber, 'settlement.SelDepAccountNumber');

        $tRes = '';
        $tQsResult = (new QueryService())->SqlExec($tFuncName, $tParam);
        if ($tQsResult['response']) {
            $tRes = $tQsResult['data'][0]['account_number'];
        }

        $this->traceLog("[getCreditAccountNumber] end with type {$pType}, merchant_id {$pMerchantId}, and sof_id {$pSofId}");
        return $tRes;
    }

    public function getDebitAccountNumber($pType, $pMerchantId = null, $pSofId = null)
    {
        $this->traceLog("[getDebitAccountNumber] start with type {$pType}, merchant_id {$pMerchantId}, and sof_id {$pSofId}");

        $tParam = [
            'acc_type' => $pType,
            'acc_prefix' => '1',
            'merchant_id' => $pMerchantId != null ? $pMerchantId : '',
            'sof_id' => $pSofId != null ? $pSofId : '',
        ];
        $tFuncName = config($this->cConfigQsSelDepAccountNumber, 'settlement.SelDepAccountNumber');

        $tRes = '';
        $tQsResult = (new QueryService())->SqlExec($tFuncName, $tParam);
        if ($tQsResult['response']) {
            $tRes = $tQsResult['data'][0]['account_number'];
        }
        
        $this->traceLog("[getDebitAccountNumber] end with type {$pType}, merchant_id {$pMerchantId}, and sof_id {$pSofId}");
        return $tRes;
    }

    private function constructRemarkReversal($pRefnum)
    {
        $this->traceLog('[constructRemarkReversal] for refnum ' . $pRefnum);

        $tTemplateRemarkReversal = config($this->cConfigTemplateRemarkReversal, 'Pembatalan transaksi #refnum#');
        return str_replace('#refnum#', $pRefnum, $tTemplateRemarkReversal);
    }

    private function constructRemarkPayment($pRefnum)
    {
        $this->traceLog('[constructRemarkPayment] for refnum ' . $pRefnum);

        $tTemplateRemarkPayment = config($this->cConfigTemplateRemarkPayment, 'Credit Adjustment #refnum#');
        return str_replace('#refnum#', $pRefnum, $tTemplateRemarkPayment);
    }
}
