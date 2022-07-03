<?php

namespace App\Http\Traits\CRM;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

use QS;

trait RequestTrait
{

    private function saveToTrxItem($data, $request_id)
    {
        $errorItem = [];
        $response = [];
        foreach ($data as $element_name => $value) {
            if ($element_name == 'data_form') {
                if ($value == 'individual') {
                    $data[$element_name] = '1';
                } else {
                    $data[$element_name] = '2';
                }
            }
        }

        foreach ($data as $element_name => $value) {

            $cekItem = QS::SqlExec("merchant.seltrxrequestitemfield", [
                "request_id" => $request_id,
                "field_name" => $element_name
            ]);
            if ($cekItem['response']) {
                // Update ke trx_item
                $trxItem = QS::SqlExec("merchant.updtrxrequestitem", [
                    "request_item_id" => $cekItem['data'][0]['request_item_id'],
                    "updated_by" => Session::get('user')->username,
                    "value" => $value
                ]);
            } else {
                // Insert ke trx_item
                $trxItem = QS::SqlExec("merchant.intrxrequestitem", [
                    "request_id" => $request_id,
                    "created_by" => Session::get('user')->username,
                    "updated_by" => Session::get('user')->username,
                    "field_name" => $element_name,
                    "value" => $value
                ]);
            }

            if ($trxItem['response'] != true) {
                $errorItem[] = $element_name;
            }
        }

        if (empty($errorItem)) {
            $response['rc'] = "0000";
            $response['rcm'] = "Sukses";
        } else {
            $errorText = implode(", ", $errorItem);
            $response['rc'] = "0005";
            $response['rcm'] = "Terdapat error pada saat penyimpanan item: " . $errorText;
        }
        return $response;
    }

    private function call4EyesEngine($requestId, $menuAccess)
    {
        $param = [
            "menu_access" => $menuAccess,
            "approval_action" => "1",
            "request_id" => $requestId,
            "user_id" => Session::get('user')->user_id,
            "catatan" => ""
        ];

        $api_token = Session::get('api_token');
        // $api_url = config('app.api_url');
        $api_url = 'http://10.168.26.10:8088';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $api_token['access_token']
        ])->post($api_url . '/4eyes-engine', $param);

        return $response->json();
    }
}
