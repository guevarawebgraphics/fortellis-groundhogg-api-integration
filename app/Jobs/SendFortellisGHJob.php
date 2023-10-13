<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Traits\APITrait;

class SendFortellisGHJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, APITrait;

    protected $arrayItems;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($arrayItems)
    {
        $this->arrayItems = $arrayItems;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $data = json_encode($this->arrayItems);
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => config('services.groundhogg.webhook_url'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        \Log::info('Job Response: ' . $response);



        
    }
}
