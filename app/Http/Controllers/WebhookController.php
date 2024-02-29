<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function index(){
        Storage::disk('public')->put('whatsapp.json', json_encode(request()->all()));
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/whatsapp.log'),
          ])->info('whatsapp Start: '.request()->method());
    }
}
