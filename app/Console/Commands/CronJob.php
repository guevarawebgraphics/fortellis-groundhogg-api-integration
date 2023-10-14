<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

use Carbon\Carbon;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use DateTime;
use DateTimeZone;

class CronJob extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_groundhogg_request:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This CRON helps to trigger middleman api, to store contacts in Groundhogg';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        // API endpoint URL
        $url = 'https://mycrmplayground.com/api-middleman/public/api/search/customers';

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);     // Return the response as a string
        curl_setopt($ch, CURLOPT_HTTPGET, 1);            // Set request type to GET
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);     // Follow redirects if any
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Depending on your setup, you may need to disable SSL verification. In production, you should NOT do this.

        // Execute cURL session
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        // Print response
        \Log::info('GH Webhook Response' . json_encode($response));
    }

    

}