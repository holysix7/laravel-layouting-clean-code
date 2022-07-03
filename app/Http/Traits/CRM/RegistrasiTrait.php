<?php

namespace App\Http\Traits\CRM;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\QueryService\Facades\QueryServiceFacades as QS;
use Illuminate\Support\Facades\Config;

trait RegistrasiTrait
{

    private function getDataRegistrasiByMerchantId($merchant_id)
    {
        $data = null;

        $res = QS::SqlExec("merchant.seldataregistrasibymerchantid", [
            "merchant_id" => $merchant_id
        ]);

        if ($res['response']) {
            $data['request_id'] = $res['data'][0]['request_id'];

            $resitem = QS::SqlExec("merchant.seltrxrequestitem", [
                "request_id" => $data['request_id']
            ]);
            if ($resitem['response']) {
                foreach ($resitem['data'] as $key => $value) {
                    if (is_null($value['file'])) {
                        $data[$value['field_name']] = $value['value'];
                    } else {
                        try {
                            $filename = explode("$merchant_id/", $value['file'])[1];
                            $url = url('/crm/registrasi/draft-doc/' . $value['request_item_id'] . '/' . $filename);
                        } catch (Exception $e) {
                            $url = null;
                        }

                        $data[$value['field_name']] = $url;
                    }
                }
            }
            $data = (object) $data;
        }

        return $data;
    }

    private function getDataRegistrasi()
    {
        $merchant_id = Session::get('user')->merchant_id;
        return $this->getDataRegistrasiByMerchantId($merchant_id);
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
        $api_address = Config::get('app.url');
        $url = $api_address.'/api/list-bank';
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

    private function getBagiHasil($merchant_id)
    {
        $data = [];

        $res = QS::SqlExec("merchant.selbagihasil", [
            "merchant_id" => $merchant_id
        ]);

        if ($res['response']) {
            foreach ($res['data'] as $row) {
                $row['url'] = empty($row['attachment_path']) ? '' : url('/crm/registrasi/bagihasil-doc/' . $row['id'] . '/' . $row['attachment_path']);
                $data[] = $row;
            }
        }

        return $data;
    }

    private function getEvidence($merchant_id)
    {
        $data = [];

        $res = QS::SqlExec("merchant.selevidence", [
            "merchant_id" => $merchant_id
        ]);

        if ($res['response']) {
            foreach ($res['data'] as $row) {
                $url = null;
                if (!empty($row['file'])) {
                    try {
                        $filename = explode("$merchant_id/", $row['file'])[1];
                        $url = empty($row['file']) ? '' : url('/crm/registrasi/evidence-doc/' . $row['merchant_evidence_id'] . '/' . $filename);
                    } catch (Exception $e) {
                        $url = null;
                    }
                }
                $data[$row['type']] = $url;
            }
        }

        return (object) $data;
    }

    private function getBankAccount($merchant_id)
    {
        $data = [];

        $res = QS::SqlExec("merchant.selbankaccount", [
            "merchant_id" => $merchant_id
        ]);

        if ($res['response']) {
            foreach ($res['data'] as $row) {
                $row['url'] = null;
                if (!empty($row['file'])) {
                    try {
                        $filename = explode("$merchant_id/", $row['file'])[1];
                        $row['url'] = empty($row['file']) ? '' : url('/crm/registrasi/account-doc/' . $row['merchant_account_id'] . '/' . $filename);
                    } catch (Exception $e) {
                        $row['url'] = null;
                    }
                }
                $data[] = $row;
            }
        }

        return $data;
    }

    private function getFileContentsFromAPI($filePath)
    {

        $api_token = Session::get('api_token');
        $api_url = Config::get('app.api_url');
        // $api_url = 'http://10.1.18.111:8088/';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $api_token['access_token'],
            'responseType' => 'blob'

        ])->get($api_url . str_replace("public", "storage", $filePath));

        return $response->getBody()->getContents();
    }

    private function getFileContentsFromLocal($filePath)
    {

        $api_token = Session::get('api_token');
        $api_url = Config::get('app.api_url');
        // $api_url = 'http://10.1.18.111/';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $api_token['access_token'],
            'responseType' => 'blob'

        ])->get($api_url . str_replace("public", "storage", $filePath));

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

        $api_address = Config::get('app.url');
        $url = $api_address.'/api/list-' . $type;
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

    private function getRegistrationStatus()
    {
        $res = QS::SqlExec("merchant.selsinglemerchant", [
            "merchant_id" => Session::get('user')->merchant_id
        ]);

        if ($res['response']) {
            $merchant = $res['data'][0];
            $is_submerchant = !is_null($merchant['parent_id']);
            $enable_registrasi = in_array($merchant['status'], [1, 6, 7]);

            return [
                'is_submerchant' => $is_submerchant,
                'enable_registrasi' => $enable_registrasi,
                'status' => $merchant['status']
            ];
        } else {
            return null;
        }
    }

    private function uploadFileToRequestItem(
        Request $request,
        $fileObjectName,
        $fieldName,
        $requestId = null
    ) {
        $api_token = Session::get('api_token');
        $api_url = Config::get('app.api_url');
        $imageFile = $request->file($fileObjectName)->getRealPath();
        $file = fopen($imageFile, 'r');

        $payload = [
            'field_name' => $fieldName,
            'merchant_id' => Session::get('user')->merchant_id
        ];

        // if (!is_null($requestId)) {
        //     $payload['request_id'] = $requestId;
        // }

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $api_token['access_token']
        ])->attach('file', $file)->post($api_url . '/merchant-data/upload', $payload);
    }

    private function addFileToRequestItem(
        Request $request,
        $fileObjectName,
        $fieldName,
        $requestType
    ) {
        $api_token = Session::get('api_token');
        $api_url = Config::get('app.api_url');
        $imageFile = $request->file($fileObjectName)->getRealPath();
        $file = fopen($imageFile, 'r');

        $payload = [
            'field_name' => $fieldName,
            'merchant_id' => Session::get('user')->merchant_id,
            'request_type' => $requestType
        ];

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $api_token['access_token']
        ])->attach('file', $file)->post($api_url . '/merchant-data/add-doc', $payload);
    }
}
