<?php

namespace App\Http\Traits\Balance;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\QueryService\Facades\QueryServiceFacades as QS;
use Illuminate\Support\Facades\Config;

trait BalanceTrait
{


    private function getBalance()
    {

        $api_token = Session::get('api_token');
        $api_url = Config::get('app.api_url');
        // $api_url = 'http://10.168.26.10:8088/';

        $param = [
            "merchant_id" => Session::get('user')->merchant_id
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $api_token['access_token']
        ])->post($api_url . 'account/balance', $param);

        return $response->json();
    }

    private function getTotalBalance()
    {
        $res = $this->getBalance();
        $balance = 0;
        if ($res['rc'] == '0000') {
            $responseBalance = $res['responseData'];
            $balance = $responseBalance['Total_balance'] + $responseBalance['pending_balance'];
        }
        return $balance;
    }

    private function getActualBalance()
    {
        $res = $this->getBalance();
        $balance = 0;
        if ($res['rc'] == '0000') {
            $balance = $res['responseData']['total_actual_balance'];
        }
        return $balance;
    }

    private function getBalanceHistory($start_at, $end_at)
    {

        $api_token = Session::get('api_token');
        $api_url = Config::get('app.api_url');
        // $api_url = 'http://10.168.26.10:8088/';

        $param = [
            "from_date" => $start_at,
            "until_date" => $end_at,
            "merchant_id" => Session::get('user')->merchant_id
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $api_token['access_token']
        ])->post($api_url . 'account/history-balance', $param);

        return $response->json();
    }

    private function getParamBiayaTransfer()
    {

        $res = QS::SqlExec("merchant.selparambiayatransfer", [
            "merchant_id" => Session::get('user')->merchant_id
        ]);
        $params = [];

        if ($res['response']) {
            foreach ($res['data'] as $row) {
                $params[$row['param_name']] = $row['value'];
            }
        }
        return $params;
    }

    private function getBiayaTransferBedaBank($bankId)
    {
        $params = $this->getParamBiayaTransfer();
        if (count($params) == 0) throw new Exception();

        if (in_array("$bankId", ['12', '66', '43', '104'])) {
            return $params['himbara_fee_transfer'];
        } else {
            return $params['non_himbara_fee_transfer'];
        }
    }
}
