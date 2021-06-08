<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VoiceGrant;
use Twilio\TwiML\VoiceResponse;

class CallController extends Controller
{
    public function token()
    {
        $accountSid = config('services.twilio')['accountSid'];
        $apiKey = config('services.twilio')['apiKey'];
        $apiSecret = config('services.twilio')['apiSecret'];
        $this->accessToken=new AccessToken($accountSid, $apiKey, $apiSecret, 3600, 'identity');
        $applicationSid = config('services.twilio')['applicationSid'];

        $this->accessToken->setIdentity('customer');
        // Create Voice grant
        $voiceGrant = new VoiceGrant();
        $voiceGrant->setOutgoingApplicationSid($applicationSid);

        // Add grant to token
        $this->accessToken->addGrant($voiceGrant);

        // render token to string
        $token = $this->accessToken->toJWT();

        return response()->json(['status' => true,'token' => $token]);
    }

    public function scheduleCall(Request $request)
    {
        $Ticket = new Ticket();
        $Ticket->schedule_datetime = $request->input('schedule_date').' '.$request->input('schedule_time');
        $Ticket->ip = $request->ip();
        $Ticket->call_status = 'Scheduled';
        $Ticket->status = 'pending';
        $Ticket->fill($request->all())->save();
        return response()->json(['status' => true]);
    }
    public function failCall(Request $request)
    {
        $Ticket = new Ticket();
        $Ticket->ip = $request->ip();
        $Ticket->call_status = 'Failed';
        $Ticket->status = 'pending';
        $Ticket->fill($request->all())->save();
        return response()->json(['status' => true]);
    }
    /**
     * Process a new call
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function newCall(Request $request)
    {
        Log::info([Route::currentRouteName() => collect($request->all())->toJson()]);
        if($request->input('widget')){
            return $this->widgetCall($request);
        }else{
            $response = new VoiceResponse();
            $callerIdNumber = config('services.twilio')['number'];
            $dial = $response->dial(null, ['callerId'=>$callerIdNumber]);
            $phoneNumberToDial = $request->input('phoneNumber');
            if (isset($phoneNumberToDial)) {
                $dial->number($phoneNumberToDial);
            } else {
                $dial->client('customer');
            }
            return $response;
        }

    }

    public function widgetCall($request)
    {
        $response = new VoiceResponse();
        $dial = $response->dial(null, ['callerId'=>$request->input('From')]);
        $client = $dial->client('support');
        $Ticket = new Ticket();
        $Ticket->request = collect($request->all())->toJson();
        $Ticket->ip = $request->ip();
        $Ticket->status = 'pending';
        $Ticket->call_status = 'Calling';
        $Ticket->response = $response->__toString();
        $Ticket->referenceCall = $request->CallSid;
        $Ticket->fill($request->all())->save();
        $client->parameter(['name' => 'tid', 'value' => $Ticket->id]);
        $client->parameter(['name' => 'customer_name', 'value' => $Ticket->customer_name]);
        $client->parameter(['name' => 'customer_phone', 'value' => $Ticket->customer_phone]);
        $client->parameter(['name' => 'topic', 'value' => $Ticket->topic]);
        return $response;
    }

    public function fallback(Request $request)
    {
        if($request->has('CallSid')){
            Ticket::where('referenceCall',$request->input('CallSid'))->where('call_status','Calling')->update(['call_status'=>'No answer']);
        }
        Log::info([Route::currentRouteName() => collect($request->all())->toJson()]);
    }

}
