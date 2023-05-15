<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class BridgeForVendorController extends Controller
{
    public function getToken()
    {

        $url = "http://bridgeforvendor.com/mirai_online_api/public/api/login?username=PI2009022&password=PI2009022";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
        ));

        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $login = json_decode($json_response);

        return $login->access_token;

    }

    public function getApi($token, $link, $method, $param)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'http://bridgeforvendor.com/mirai_online_api/public/api/' . $link);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $token,
        ));
        if ($param != '') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        }

        $data = curl_exec($curl);
        curl_close($curl);

        return json_decode($data);

    }
}
