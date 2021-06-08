<?php

namespace App\Http\Controllers\Twilio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class ApplicationController extends Controller
{
    protected $twilio;
    public function __construct()
    {
        $this->twilio = config('services.twilio');
    }

    public function update(Request $request)
    {
        $host = 'https://'.$request->getHttpHost();
        $client = new Client($this->twilio['accountSid'], $this->twilio['accountAuthtoken']);
        $client->applications($this->twilio['applicationSid'])->update(
            [
                'VoiceUrl' => $host . '/support/call',
                'VoiceMethod' => 'GET',
                'VoiceFallbackUrl' => $host . '/support/fallback',
                'VoiceFallbackMethod' => 'GET',
                'StatusCallback' => $host . '/support/callback',
                'StatusCallbackMethod' => 'GET',
            ]
        );
        dd('done');
    }
}
