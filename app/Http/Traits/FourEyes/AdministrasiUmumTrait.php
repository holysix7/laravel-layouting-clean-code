<?php

namespace App\Http\Traits\FourEyes;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\QueryService\Facades\QueryServiceFacades as QS;
use Illuminate\Support\Facades\Config;

trait AdministrasiUmumTrait
{

    private function getDataRegistrasi($merchant_id, $request_id)
    {
        $data = null;

        $res = QS::SqlExec("merchant.seltrxrequest", [
            "merchant_id" => $merchant_id,
            "request_id" => $request_id,
        ]);

        if ($res['response']) {
            $data['request_id'] = $res['data'][0]['request_id'];
            $resitem = QS::SqlExec("merchant.seltrxrequestitem", [
                "request_id" => $data['request_id']
            ]);
            if ($resitem['response']) {
                foreach ($resitem['data'] as $key => $value) {
                    $data[$value['field_name']] = is_null($value['file']) ? $value['value'] : (object) $value;
                }
            }
            $data = (object) $data;
        }

        return $data;
    }

    private function getBusinessFields()
    {
        $businessFieldOpt = [];
        $businessField = QS::SqlExec("merchant.seldatalookup", ["code" => "flag_business_field", "is_active" => "t"]);
        if ($businessField['response'] == true) {
            $businessFieldOpt = $businessField['data'];
        }
        return $businessFieldOpt;
    }

    private function getCompanyType()
    {
        $companyTypeOpt = [];
        $companyType = QS::SqlExec("merchant.seldatalookup", ["code" => "flag_company_type", "is_active" => "t"]);
        if ($companyType['response'] == true) {
            $companyTypeOpt = $companyType['data'];
        }
        return $companyTypeOpt;
    }

    private function getIdentitas()
    {
        $idx = [];
        $identitas = QS::SqlExec("merchant.seldatalookup", ["code" => "flag_identity_type", "is_active" => "t"]);
        if ($identitas['response'] == true) {
            $idx = $identitas['data'];
        }
        return $idx;
    }

    private function getBank()
    {
        $api_url = Config::get('app.url');
        $url = $api_url.'/api/list-bank';
        $param = [
            'id' => '',
            'name' => '',
        ];

        $data = [];
        $res = sendAPI($param, $url);
        if ($res && $res['success']) {
            $data = $res['data'];
        }

        return $data;
    }

    private function getDocStream($itemId)
    {
        $api_token = Session::get('api_token');
        $api_url = Config::get('app.api_url');
        // $api_url = 'http://10.1.18.111:8088';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $api_token['access_token'],
            'responseType' => 'blob'

        ])->get($api_url . '/merchant-data/doc/' . $itemId);

        return $response->getBody()->getContents();
    }

    private function filterOption($array, $value, $field)
    {
        return
            array_filter($array, function ($arr) use ($value, $field) {
                return $value == $arr[$field];
            });
    }

    // 4 type :
    // provinsi, kabkota, kecamatan, & kelurahan
    private function getWilayah($type, $id)
    {
        if (empty($id)) return null;

        $api_url = Config::get('app.url');
        $url = $api_url.'/api/list-' . $type;
        $param = ['id' => $id];

        $res = sendAPI($param, $url);
        $data = null;

        if ($res && $res['success']) {
            if (is_array($res['data']) && count($res['data']) > 0) {
                $data = (object) $res['data'][0];
            }
        }

        return $data;
    }

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

    private function saveToTrxItemSof($data, $request_id)
    {
        $errorItem = [];
        $response = [];

        foreach ($data as $element_name => $value) {
            $cekItem = QS::SqlExec("sof.seltrxrequestitemfield", [
                "request_id" => $request_id,
                "field_name" => $element_name
            ]);
            if ($cekItem['response']) {
                // Update ke trx_item
                $trxItem = QS::SqlExec("sof.updtrxrequestitem", [
                    "request_item_id" => $cekItem['data'][0]['request_item_id'],
                    "updated_by" => Session::get('user')->username,
                    "value" => $value
                ]);
            } else {
                // Insert ke trx_item
                $trxItem = QS::SqlExec("sof.intrxrequestitem", [
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
            $response['rcm'] = "Data berhasil disimpan";
        } else {
            $errorText = implode(", ", $errorItem);
            $response['rc'] = "0005";
            $response['rcm'] = "Terdapat error pada saat penyimpanan item: " . $errorText;
        }
        return $response;
    }

    private function getDataRegistrasiSof($sof_id, $request_id)
    {
        $data = null;

        $res = QS::SqlExec("sof.seltrxrequest", [
            "sof_id" => $sof_id,
            "request_id" => $request_id,
        ]);

        if ($res['response']) {
            $data['request_id'] = $res['data'][0]['request_id'];
            $resitem = QS::SqlExec("sof.seltrxrequestitem", [
                "request_id" => $data['request_id']
            ]);
            if ($resitem['response']) {
                foreach ($resitem['data'] as $key => $value) {
                    $data[$value['field_name']] = is_null($value['file']) ? $value['value'] : (object) $value;
                }
            }
            $data = (object) $data;
        }

        return $data;
    }
}
