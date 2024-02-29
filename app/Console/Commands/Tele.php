<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class Tele extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tele';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Telegram::bot('mybot')->getMe();
        $updates = $response->getUpdates();
        $telegram = Telegram::bot('mybot');
        $response = $telegram->sendMessage([
            'chat_id' => '@adidev83',
            'text' => 'Hello World'
        ]);
        
        $messageId = $response->getMessageId();
        dump($updates);
        dump($messageId);
        dd($response);
    }
}
