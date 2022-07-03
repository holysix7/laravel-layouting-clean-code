<?php

// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

function saveLogActivity($params){
    $parameters = [
        'created_by'         => Session::get('user')->id,
        'created_by_name'    => Session::get('user')->name,
        'created_at'         => date('Y-m-d H:i:s'),
        'url'                => url()->current(),
        'description'        => $params
    ];
    $api = 'http://127.0.0.1:8080/api/store-activity';
    
    return sendAPI($parameters, $api);
}

function sendAPI($data, $url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json'
        )
    );
    sleep(0.5);
    $result = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($result, true);
}
