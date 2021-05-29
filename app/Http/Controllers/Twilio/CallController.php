<?php

namespace App\Http\Controllers\Twilio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;

class CallController extends Controller
{
    protected $twilio;

    public function __construct() {
        $this->twilio = config('services.twilio');
    }

    public function incoming()
    {
        $response = new VoiceResponse();
        $gather = $response->gather(array('numDigits' => 1, 'action' => 'twilio/call/enqueue', 'timeout'=>5));
        $gather->say('For IT, press 1. For Programmer, press 2.');
        // $response->redirect(url('api/call'));
        return $response;
    }

    public function enqueue(Request $request)
    {
        $response = new VoiceResponse();
        $gather = $response->enqueue(array('workflowSid' => $this->twilio->workflowSid));
        $gather->task(array("selected_skill"=> $request->has('Digits') && $request->Digits ==2 ? 'Programmer' : 'IT'));
        return $response;
    }
}
