<?php

namespace App\Http\Controllers\Twilio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class TaskController extends Controller
{
    protected $twilio;
    protected $client;

    public function __construct() {
        $this->twilio = config('services.twilio');
        $this->client = new Client($this->twilio['accountSid'], $this->twilio['accountAuthtoken']);
    }

    public function create(Request $request)
    {

        $task = $this->client->taskrouter
            ->workspaces($this->twilio['workspaceSid'])
            ->tasks
            ->create(array(
                'attributes' => '{"selected_skill": "IT"}',
                'workflowSid' => $this->twilio['workflowSid'],
            ));
        return response()->json("created a new task");
    }

    public function assignment(Request $request)
    {
        return response()->json([
            'instruction' => 'dequeue',
            'post_work_activity_sid' => $this->twilio->activities->wrapup,
            'from' => 'customer' // a verified phone number from your twilio account
        ]);
    }

    public function accept(Request $request)
    {
        $this->client->taskrouter
            ->workspaces($this->twilio['workspaceSid'])
            ->tasks($request->taskSid)
            ->reservations($request->reservationSid)
            ->update(['reservationStatus' => 'accepted']);
    }
}
