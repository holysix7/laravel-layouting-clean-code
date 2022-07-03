<?php

/**
 * Config untuk settlement
 */

return [
    'deposit_host' => env('DEPOSIT_HOST', '10.168.26.10'),

    'deposit_port' => env('DEPOSIT_PORT', '13130'),

    'account_deposit_type' => [
        'ditahan' => '5',
        'global' => '4',
        'pg' => '3',
        'penampungan' => '2',
        'merchant' => '1',
        'sof' => '1',
        'submerchant' => '1',
    ],

    'deposit' => [
        'host' => env('DEPOSIT_HOST', '10.168.26.10'),
        'isactive' => 'true',
        'port' => env('DEPOSIT_PORT', '13130'),
    ],

    'flag' => [
        'payment' => '4',
        'reversal' => '3',
    ],

    'status' => [
        'payment' => 'Dilunaskan',
        'reversal' => 'Dibatalkan',
        'penyesuaian-dana' => 'Completed by adjustment',
    ],

    'template' => [
        'description' => [
            'payment' => '[SUCCESS] Berhasil melunaskan transaksi sof_id #sof_id# dengan refnum #refnum# di tanggal #date#',
            'reversal' => '[SUCCESS] Berhasil membatalkan payment dengan ID #payment_id#',
            'suspect' => '[SUSPECT] Tidak dapat melunaskan transaksi sof_id #sof_id# dengan refnum #refnum# di tanggal #date#',
        ],

        'remark' => [
            'payment' => 'Credit Adjustment #refnum#',
            'reversal' => 'Pembatalan transaksi #refnum#',
        ],
    ],

    'url' => [
        'jasatarif' => env('URL_JASATARIF', 'http://10.168.26.10:13235/getTarif'),
    ],

    'qs' => [
        'ins_payment_adjustment' => 'settlement.InsPaymentAdjustment',
        'sel_dep_account_number' => 'settlement.SelDepAccountNumber',
        'sel_details' => 'settlement.SelDetails',
        'sel_inquiry' => 'settlement.SelInquiry',
        'sel_payment' => 'settlement.SelPayment',
        'sel_recon' => 'settlement.SelRecon',
        'upd_payment_flag' => 'settlement.UpdPaymentFlag',
        'upd_recon_recompare' => 'settlement.UpdReconRecompare',
    ],
];
