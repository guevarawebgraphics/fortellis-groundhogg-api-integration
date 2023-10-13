<?php

namespace App\Http\Traits;

use URL;

/**
 * Class APITrait
 * @package App\Http\Traits
 * @author Guevara Web Graphics Studio
 */
trait APITrait
{
    
    private function makeAPIRequest($url, $headers, $method, $data) {
        try {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => $headers,
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            \Log::info('JSON ' . $data );
            return $response;

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return [
                'data' => NULL,
                'message' => $e->getMessage(),
                'status' => false
            ];
        }
    }
}
