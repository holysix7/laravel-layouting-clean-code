<?php

namespace App\Services\ReconSync;

use App\QueryService\QueryService;
use App\Services\Deposit\JournalTrait;
use Exception;
use Illuminate\Support\Facades\Log;

class ReconSync
{
    use JournalTrait;

    // constants, init in construct
    private $cAccountTypeGlobal;
    private $cAccountTypePg;
    private $cAccountTypePenampungan;
    private $cAccountTypeMerchant;
    private $cAccountTypeSubmerchant;
    private $cAccountTypeSof;
    private $cAccountTypeDitahan;
    private $cFlagPayment;
    private $cFlagReversal;
    private $cStatusPayment;
    private $cStatusReversal;
    private $cStatusRecomparing;
    private $cTemplateDescriptionPayment;
    private $cTemplateDescriptionReversal;
    private $cTemplateDescriptionSuspect;
    private $cTemplateRemarkPayment;
    private $cTemplateRemarkReversal;
    private $cUrlJasaTarif;
    private $cQsInsPaymentAdjustment;
    private $cQsSelRecon;
    private $cQsSelDetails;
    private $cQsSelInquiry;
    private $cQsSelPayment;
    private $cQsUpdPaymentFlag;
    private $cQsUpdReconRecompare;

    // attributes, init in construct
    private $mQueryService;
    private $mReconId;
    private $mRecon = null;
    private $mReconItems = null;

    // attributes for synchronizing
    private $mDescription = '';
    private $mPayItems = [];
    private $mRevItems = [];

    // attributes result
    public $mIsSuccess = false;

    public function __construct(int $pReconId)
    {
        $this->traceLog('[__construct] start');

        $this->mReconId = $pReconId;

        $this->cAccountTypeGlobal = config('settlement.account_deposit_type.global', '4');
        $this->cAccountTypePg = config('settlement.account_deposit_type.pg', '3');
        $this->cAccountTypePenampungan = config('settlement.account_deposit_type.penampungan', '2');
        $this->cAccountTypeMerchant = config('settlement.account_deposit_type.merchant', '1');
        $this->cAccountTypeSubmerchant = config('settlement.account_deposit_type.submerchant', '1');
        $this->cAccountTypeSof = config('settlement.account_deposit_type.sof', '1');
        $this->cAccountTypeDitahan = config('settlement.account_deposit_type.ditahan', '5');
        $this->cFlagPayment = config('settlement.flag.payment', '4');
        $this->cFlagReversal = config('settlement.flag.reversal', '3');
        $this->cStatusPayment = config('settlement.status.payment', 'Dilunaskan');
        $this->cStatusRecomparing = config('settlement.status.recomparing', 'Recomparing');
        $this->cStatusReversal = config('settlement.status.reversal', 'Dibatalkan');
        $this->cTemplateDescriptionPayment = config('settlement.template.description.payment', '[SUCCESS] Berhasil melunaskan transaksi sof_id #sof_id# dengan refnum #refnum# di tanggal #date#\n');
        $this->cTemplateDescriptionReversal = config('settlement.template.description.reversal', '[SUCCESS] Berhasil membatalkan payment dengan ID #payment_id#\n');
        $this->cTemplateDescriptionSuspect = config('settlement.template.description.suspect', '[SUSPECT] Tidak dapat melunaskan transaksi sof_id #sof_id# dengan refnum #refnum# di tanggal #date#\n');
        $this->cTemplateRemarkPayment = config('settlement.template.remark.payment', 'Credit Adjustment #refnum#');
        $this->cTemplateRemarkReversal = config('settlement.template.remark.reversal', 'Pembatalan transaksi #refnum#');
        $this->cUrlJasaTarif = config('settlement.url.jasatarif', 'http://10.168.26.10:13235/getTarif');
        $this->cQsInsPaymentAdjustment = config('settlement.qs.ins_payment_adjustment', 'settlement.InsPaymentAdjustment');
        $this->cQsSelRecon = config('settlement.qs.sel_recon', 'settlement.SelRecon');
        $this->cQsSelDetails = config('settlement.qs.sel_details', 'settlement.SelDetails');
        $this->cQsSelDepAccountNumber = config('settlement.qs.sel_dep_account_number', 'settlement.SelDepAccountNumber');
        $this->cQsSelInquiry = config('settlement.qs.sel_inquiry', 'settlement.SelInquiry');
        $this->cQsSelPayment = config('settlement.qs.sel_payment', 'settlement.SelPayment');
        $this->cQsUpdPaymentFlag = config('settlement.qs.upd_payment_flag', 'settlement.UpdPaymentFlag');
        $this->cQsUpdReconRecompare = config('settlement.qs.upd_recon_recompare', 'settlement.UpdReconRecompare');
        $this->mQueryService = new QueryService();

        $this->initData();

        $this->traceLog('[__construct] end');
    }

    public function synchronize()
    {
        $this->traceLog('[synchronize] start');

        if ($this->mRecon == null || $this->mReconItems == null) {
            throw new Exception('Init data is failed.');
        }

        $this->initPayRevItems();
        $this->reversal();
        $this->payment();
        $this->updateReconRecompare();

        $this->traceLog('[synchronize] end');
    }

    private function initData()
    {
        $this->traceLog('[initData] start');

        $this->retrieveRecon();
        $this->retrieveReconItems();
        $this->constructReconItemStatuses();

        $this->traceLog('[initData] end');
    }

    private function retrieveRecon()
    {
        $this->traceLog('[retrieveRecon] start');

        $tParam = [
            'recon_id' => $this->mReconId,
        ];
        $tFuncName = $this->cQsSelRecon;

        $tRes = [];
        $tQsResult = $this->mQueryService->SqlExec($tFuncName, $tParam);
        if ($tQsResult['response']) {
            $tRes = $tQsResult['data'][0];
        }
        $this->mRecon = $tRes;

        $this->traceLog('[retrieveRecon] end');
    }

    private function retrieveReconItems()
    {
        $this->traceLog('[retrieveReconItems] start');

        $tParam = [
            'recon_id' => $this->mReconId,
        ];
        $tFuncName = $this->cQsSelDetails;

        $tRes = [];
        $tQsResult = $this->mQueryService->SqlExec($tFuncName, $tParam);
        if ($tQsResult['response']) {
            $tRes = $tQsResult['data'];
        }
        $this->mReconItems = $tRes;

        $this->traceLog('[retrieveReconItems] end');
    }

    private function constructReconItemStatuses()
    {
        $this->traceLog('[constructReconItemStatuses] start');

        $tFinalResult = collect([]);
        $tDetails = collect($this->mReconItems);
        foreach ($tDetails as $tDetail) {
            $tDetail = collect($tDetail);
            $tStatuses = collect([]);

            if (!$tDetail['isbalance']) {
                if ($tDetail['isrefund']) {
                    $tStatus = collect([
                        'action' => 'Dilunaskan',
                        'amount' => $tDetail['sof_amount']
                    ]);
                    $tStatuses->push($tStatus);
                }
                if ($tDetail['pg_status']) {
                    $tStatus = collect([
                        'action' => 'Dibatalkan',
                        'amount' => $tDetail['pg_amount']
                    ]);
                    $tStatuses->push($tStatus);
                }
            }

            $tDetail->put('status', $tStatuses);
            $tFinalResult->push($tDetail);
        }
        $this->mReconItems = $tFinalResult;

        $this->traceLog('[constructReconItemStatuses] end');
    }

    private function reversal()
    {
        $this->traceLog('[reversal] start');

        foreach ($this->mRevItems as $tItem) {
            $tPaymentId = $tItem['payment_id'];
            $this->reversePayment($tPaymentId);
            $this->mDescription .= $this->constructDescriptionReversal($tPaymentId);
            $this->saveJournalReversal($tItem);
        }

        $this->traceLog('[reversal] end');
    }

    private function payment()
    {
        $this->traceLog('[payment] start');

        foreach ($this->mPayItems as $tItem) {
            $tSofId = $this->mRecon['sof_id'];
            $tSofRefnum = $tItem['sof_refnum'];
            $tSofDate = $tItem['sof_date'];

            $tPayment = $this->getPaymentByReconItem($tSofId, $tSofRefnum, $tSofDate);
            if ($tPayment != null) {
                $this->adjustmentFromPayment($tPayment);
                $this->mDescription .= $this->constructDescriptionPayment($tSofId, $tSofRefnum, $tSofDate);
                $this->saveJournalAdjustment($this->mRecon, $tItem, $tPayment);
            } else {
                $tInquiry = $this->retrieveInquiry($tSofId, $tSofRefnum, $tSofDate);
                if ($tInquiry != null) {
                    $this->adjustmentFromInquiry($tInquiry);
                    $this->mDescription .= $this->constructDescriptionPayment($tSofId, $tSofRefnum, $tSofDate);
                    $this->saveJournalAdjustment($this->mRecon, $tItem, $tInquiry);
                } else {
                    $this->mDescription .= $this->constructDescriptionSuspect($tSofId, $tSofRefnum, $tSofDate);
                }
            }
        }

        $this->traceLog('[payment] end');
    }

    public function adjustmentFromInquiry(array $pInquiry)
    {
        $this->traceLog('[adjustmentFromInquiry] start with inquiry ' . json_encode($pInquiry));

        $tSofId = $pInquiry['sof_id'];
        $tMerchantId = $pInquiry['merchant_id'];
        $tPaymentMethodId = $pInquiry['payment_method_id'];
        $tAmount = $pInquiry['amount'];
        $tBiayaLayanan = '0';
        $tJasaTarif = $this->getJasaTarif($tSofId, $tMerchantId, $tPaymentMethodId, $tAmount, $tBiayaLayanan);
        $tPembagianJasaTarif = $tJasaTarif['detail'];

        $tParam = [
            'creator' => session('user')->user_id,
            'inq_id' => $pInquiry['inquiries_id'],
            'mrch_id' => $tMerchantId,
            'pay_method_id' => $tPaymentMethodId,
            'card_num' => $pInquiry['card_number'],
            'va_num' => $pInquiry['va_number'],
            'refnum' => $pInquiry['reference_number'],
            'trf_amount' => $tAmount,
            'transact_amount' => $pInquiry['total'],
            'fee_trf' => $pInquiry['fee_transfer'],
            'admin_fee' => '0',
            'amount' => $tAmount,
            'tax' => $pInquiry['tax'],
            'total_amount' => $pInquiry['total'],
            'flag' => $this->cFlagPayment,
            'transact_at' => $pInquiry['created_at'],
            'settled_at' => $pInquiry['created_at'],
            'recon_at' => now()->format('Y-m-d H:m:s'),
            'isrecon' => '1',
            'pay_refnum' => $pInquiry['reference_number'],
            'app_link_id' => $pInquiry['app_link_id'],
            'sof_id' => $tSofId,
            'pg_fee' => $tPembagianJasaTarif['pg'],
            'mrch_fee' => $tPembagianJasaTarif['merchant'],
            'sub_mrch_fee' => $tPembagianJasaTarif['submerchant'],
            'sof_fee' => $tPembagianJasaTarif['sof'],
            'stan' => $pInquiry['trace_number'],
            'phone_number' => $pInquiry['phone_number'],
            'customer_name' => $pInquiry['customer_name'],
            'customer_email' => $pInquiry['customer_email'],
        ];
        $tFuncName = $this->cQsInsPaymentAdjustment;

        $tQsResult = $this->mQueryService->SqlExec($tFuncName, $tParam);
        if (!$tQsResult['response']) {
            throw new Exception("Error when doing payment adjustment for inquiries_id {$pInquiry['inquiries_id']}");
        }

        $this->traceLog('[adjustmentFromInquiry] end with inquiry ' . json_encode($pInquiry));
    }

    public function retrieveInquiry(string $pSofId, string $pRefnum, string $pDate)
    {
        $this->traceLog("[retrieveInquiry] start with sof_id {$pSofId}, refnum {$pRefnum}, and date {$pDate}.");

        $tParam = [
            'sof_id' => $pSofId,
            'refnum' => $pRefnum,
            'date' => $pDate,
        ];
        $tFuncName = $this->cQsSelInquiry;

        $tRes = null;
        $tQsResult = $this->mQueryService->SqlExec($tFuncName, $tParam);
        if ($tQsResult['response']) {
            $tRes = $tQsResult['data'][0];
        } else {
            Log::warning("Error when retrieving inquiry by sof_id {$pSofId}, refnum {$pRefnum}, and date {$pDate}.");
        }

        $this->traceLog("[retrieveInquiry] end with sof_id {$pSofId}, refnum {$pRefnum}, and date {$pDate}.");
        return $tRes;
    }

    private function constructDescriptionPayment($pSofId, $pRefnum, $pDate)
    {
        $this->traceLog("[constructDescriptionPayment] for sof_id {$pSofId}, refnum {$pRefnum}, date {$pDate}");
        $tDescription = str_replace('#sof_id#', $pSofId, $this->cTemplateDescriptionPayment);
        $tDescription = str_replace('#refnum#', $pRefnum, $tDescription);
        $tDescription = str_replace('#date#', $pDate, $tDescription);
        return $tDescription . "\r";
    }

    private function constructDescriptionReversal($pPaymentId)
    {
        $this->traceLog('[constructDescriptionReversal] for payment_id ' . $pPaymentId);
        return str_replace('#payment_id#', $pPaymentId, $this->cTemplateDescriptionReversal) . "\r";
    }

    private function constructDescriptionSuspect($pSofId, $pRefnum, $pDate)
    {
        $this->traceLog("[constructDescriptionSuspect] for sof_id {$pSofId}, refnum {$pRefnum}, date {$pDate}");
        $tDescription = str_replace('#sof_id#', $pSofId, $this->cTemplateDescriptionSuspect);
        $tDescription = str_replace('#refnum#', $pRefnum, $tDescription);
        $tDescription = str_replace('#date#', $pDate, $tDescription);
        return $tDescription . "\r";
    }

    private function reversePayment($pPaymentId)
    {
        $this->traceLog('[reversePayment] start with payment_id ' . $pPaymentId);

        $tParam = [
            'payment_id' => $pPaymentId,
            'flag' => $this->cFlagReversal,
        ];
        $tFuncName = $this->cQsUpdPaymentFlag;

        $tQsResult = $this->mQueryService->SqlExec($tFuncName, $tParam);
        if (!$tQsResult['response']) {
            $this->traceLog('[reversePayment] error: result code from QueryService is failed.');
            throw new Exception("Error when doing payment reversal for payment_id {$pPaymentId}");
        }

        $this->traceLog('[reversePayment] end with payment_id ' . $pPaymentId);
    }

    private function initPayRevItems()
    {
        $this->traceLog('[initPayRevItems] start');

        foreach ($this->mReconItems as $tReconItem) {
            if ($this->isReversalItem($tReconItem)) {
                array_push($this->mRevItems, $tReconItem);
            }
            if ($this->isPaymentItem($tReconItem)) {
                array_push($this->mPayItems, $tReconItem);
            }
        }

        $this->traceLog('[initPayRevItems] end');
    }

    private function isReversalItem($pReconItem)
    {
        $this->traceLog('[isReversalItem] start with recon_item_id ' . $pReconItem['recon_item_id']);

        $tResult = false;
        if (count($pReconItem['status']) > 0) {
            foreach ($pReconItem['status'] as $tStatus) {
                $tAction = $this->cStatusReversal;
                if ($tStatus['action'] == $tAction) {
                    $tResult = true;
                    break;
                }
            }
        }

        $this->traceLog('[isReversalItem] end with recon_item_id ' . $pReconItem['recon_item_id']);
        return $tResult;
    }

    private function isPaymentItem($pReconItem)
    {
        $this->traceLog('[isPaymentItem] start with recon_item_id ' . $pReconItem['recon_item_id']);

        if (count($pReconItem['status']) > 0) {
            foreach ($pReconItem['status'] as $tStatus) {
                $tAction = $this->cStatusPayment;
                if ($tStatus['action'] == $tAction) {
                    return true;
                }
            }
        }

        $this->traceLog('[isPaymentItem] end with recon_item_id ' . $pReconItem['recon_item_id']);
        return false;
    }

    private function getPaymentByReconItem(string $pSofId, string $pRefnum, string $pDate)
    {
        $this->traceLog("[getPaymentByReconItem] start with sof_id {$pSofId}, refnum {$pRefnum}, and date {$pDate}");

        $tParam = [
            'sof_id' => $pSofId,
            'refnum' => $pRefnum,
            'date' => $pDate,
        ];
        $tFuncName = $this->cQsSelPayment;

        $tRes = null;
        $tQsResult = $this->mQueryService->SqlExec($tFuncName, $tParam);
        if ($tQsResult['response']) {
            $tRes = $tQsResult['data'][0];
        } else {
            Log::warning("Error when retrieving payment by sof_id {$pSofId}, refnum {$pRefnum}, and date {$pDate}.");
        }

        $this->traceLog("[getPaymentByReconItem] end with sof_id {$pSofId}, refnum {$pRefnum}, and date {$pDate}");
        return $tRes;
    }

    private function adjustmentFromPayment($pPayment)
    {
        $this->traceLog('[adjustmentFromPayment] start with payment ' . json_encode($pPayment));

        $tParam = [
            'payment_id' => $pPayment['payment_id'],
            'flag' => $this->cFlagPayment,
        ];
        $tFuncName = $this->cQsUpdPaymentFlag;

        $tQsResult = $this->mQueryService->SqlExec($tFuncName, $tParam);
        if (!$tQsResult['response']) {
            throw new Exception("Error when doing payment adjustment for payment id {$pPayment['payment_id']}");
        }

        $this->traceLog('[adjustmentFromPayment] end with payment ' . json_encode($pPayment));
    }

    private function updateReconRecompare()
    {
        $this->traceLog('[updateReconRecompare] start');

        $tParam = [
            'recon_id' => $this->mRecon['recon_id'],
            'status' => $this->cStatusRecomparing,
            'description' => $this->mDescription,
        ];
        $tFuncName = $this->cQsUpdReconRecompare;

        $tQsResult = $this->mQueryService->SqlExec($tFuncName, $tParam);
        if (!$tQsResult['response']) {
            throw new Exception("Error when updating recon recompare with recon_id {$this->mRecon['recon_id']}, status {$this->cStatusRecomparing}, and description {$this->mDescription}");
        }
        
        $this->traceLog('[updateReconRecompare] end');
    }
}
