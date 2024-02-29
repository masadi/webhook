<?php

use Illuminate\Support\Facades\Route;
use App\Notifications\PesanWhatsApp;
use Illuminate\Support\Facades\Http;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
/*
text
button
list
image
document
*/
Route::get('/test-wa/{no}', function($no){
    dd(url('images/bayi.jpg'));
    if($no == 'text'){
        $messageData = [
            'type' 		=> 'text',
            'text' 	=> ['body' => 'Kirim pesan dengan OneSender'],
        ];
    } elseif($no == 'button'){
        $buttons = collect(['/program', '/kontak', '/photo', '/dokumen'])
        ->mapWithKeys(function($item, $key){
            $buttonObj = [
                'type' 	=> 'reply',
                'reply' => [
                    'id' 	=> sprintf('btn%d_%d', $key, ($key + 1)),
                    'title' => $item,
                ]
            ];
            return [$key => $buttonObj];
        })->all();
        $messageData = [
			'type' => 'interactive',
			'interactive' => [
				'type' => 'button',
				'body' => ['text' => "Terima kasih telah menghubungi kami. 
                Berikut ini menu yang bisa dicoba"],
				'footer' => ['text' => "Pilihan menu"],
				'action' => [
					'buttons' => $buttons,
				]
			]
		];
    } elseif($no == 'list'){
        $sections = collect(
            [
                'title' => 'Unggulan',
                'options' => [
                    'Sekolah | Di kabupaten Merauke',
                    'Rumah Sakit | Di kabupaten Merauke',
                    'Penghijauan | Di kabupaten Merauke',
                ],
            ],
            [
                'title' => 'Pembangunan',
                'options' => [
                    'Jembatan | Pembangunan jembatan',
                    'Jalan | Pembangunan Jalan',
                ],
            ]
        )
        ->map(function($item, $key){
            $rows = collect($item['options'])->map(function($item, $skey) use($key) {
                $array = explode('|', $item, 2);
                $title = $array[0];
                $description = $array[1] ?? '';
                $key++;
                $skey++;
                $id = sprintf('option-%d-%d', $key, $skey);
                return [
                    'id' => $id,
                    'title' => $title,
                    'description' => $description,
                ];
            })->all();
            $section = [
                'title' => $item['title'],
                'rows' => $rows,
            ];
            return $section;
        })->all();
        $messageData = [
			'type' => 'interactive',
			'interactive' => [
				'type' => 'list',
				'body' => ['text' => "Kami memiliki program berikut:"],
				'footer' => ['text' => "Pilihan Program"],
				'action' => [
					'button' => "Program",
					'sections' => $sections,
				]
			]
		];
    } elseif($no == 'image'){
        $messageData = [
            'type' 		=> 'image',
            'image' => [
				'link' => url('images/bayi.jpg'),
				'caption' => 'Kirim pesan dengan OneSender',
			]
        ];
    } elseif($no == 'document'){
        $messageData = [
            'type' 		=> 'document',
            'link' => url('images/rekap.pdf'),
        ];
    }
	$user = User::first();
    //$user->notify(new PesanWhatsApp($messageData));
    $post_data = array_merge([
        'to' => $user->phone,
        'recipient_type' => 'individual',
    ], $messageData);
    $response = Http::withOptions([
        'verify' => false,
    ])->withHeaders([
        'onesender-key' => '37c17070-abf7-48bc-9ce6-aadba323c9a4',
    ])->withToken(config('onesender.api_key'))->retry(3, 100)->post(config('onesender.base_api_url').'/api/v1/messages', $post_data);
    echo 'Pesan terkirim';
    dd($response->object());
});
