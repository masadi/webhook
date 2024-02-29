<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function index(){
        $rawdata = file_get_contents("php://input");
		$json = json_decode($rawdata, true);
        Storage::disk('public')->put('whatsapp.json', json_encode(request()->all()));
        Storage::disk('public')->put('rawdata.json', json_encode($json));
        $post_data = [
            
        ];
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'onesender-key' => '37c17070-abf7-48bc-9ce6-aadba323c9a4',
        ])->retry(3, 100)->post('https://api.mas-adi.net/api/v1/messages', $post_data);
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/whatsapp.log'),
          ])->info('whatsapp Start: '.request()->method());
    }
}
