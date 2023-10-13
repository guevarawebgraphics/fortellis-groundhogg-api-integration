<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

use App\Jobs\SendFortellisGHJob;
use Illuminate\Support\Facades\Queue;


class APIController extends Controller
{

    public function searchCustomers() {
        
        $curlSearch = curl_init();
        $allItems = [];
        $recordCount = 0; 
        $oauthAccessToken = Session::get('fortellis_oauth_token') ? Session::get('fortellis_oauth_token') : $this->oauthToken();

        try {
            do {
                curl_setopt_array($curlSearch, array(
                    CURLOPT_URL => config('services.fortellis.search_api'),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
                        "firstName": "Jessie",
                        "lastName": "Brown"
                    }',
                    CURLOPT_HTTPHEADER => array(
                        'Subscription-Id: ' . config('services.fortellis.subscription_id'),
                        'Authorization: Bearer ' . $oauthAccessToken,
                        'Content-Type: application/json'
                    ),
                ));

                $searchResponse = curl_exec($curlSearch);
                
                if (curl_errno($curlSearch)) {
                    return json_encode(['status'    =>  false, 'message'    => curl_error($curlSearch) ]);
                }

                $decodedResponse = json_decode($searchResponse);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return json_encode(['status'    =>  false, 'message'    => json_last_error_msg() ]);
                }

                // Append the items from the current page to our allItems array
                foreach ($decodedResponse->items as $item) {
                    if ($recordCount >= 10) {
                        break 2; // Exit both the foreach and do-while loop
                    }
                    $allItems[] = $item;
                    
                    $job = new SendFortellisGHJob($item);
                    Queue::push($job);

                    $recordCount++;
                }

                // Check if there's a "next" link available
                $nextLink = array_filter($decodedResponse->links, function ($link) {
                    return $link->rel == "next";
                });

                // If we have a next link, update the searchApiUrl to fetch the next page
                if ($nextLink) {
                    $searchApiUrl = current($nextLink)->href;
                } else {
                    $searchApiUrl = null; // To stop the loop
                }
            } while ($searchApiUrl && $recordCount < 10);
            // } while ($searchApiUrl);

            curl_close($curlSearch);

            // Now, $allItems contains up to 50 records.
            return json_encode($allItems);
            // return $recordCount;

        } catch (\Exception $e) {
            return json_encode(['status'    =>  false, 'message'    =>  $e->getMessage() ]);
        }

    }

    private function oauthToken() {
        
        $curlOAuth = curl_init();

        curl_setopt_array($curlOAuth, array(
        CURLOPT_URL => config('services.fortellis.bearer_url'),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials&scope=anonymous&client_id='.config('services.fortellis.key').'&client_secret='.config('services.fortellis.secret'),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
        ),
        ));

        $oauthResponse = curl_exec($curlOAuth);

        curl_close($curlOAuth);

        // Decode the JSON response
        $responseArray = json_decode($oauthResponse, true);  // true parameter makes it return associative array


        $accessToken = isset($responseArray['access_token']) ? $responseArray['access_token'] : null;

        Session::put('fortellis_oauth_token', $accessToken );

        return $accessToken;

    }
}
