<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Twilio\Jwt\TaskRouter\WorkerCapability;
use Twilio\Rest\Client;
use Twilio\Rest\Taskrouter\V1\Workspace\ActivityList;

class DashboardController extends Controller
{
    protected $twilio;
    protected $client;

    public function __construct() {
        $this->twilio = config('services.twilio');
        $this->client = new Client($this->twilio['accountSid'], $this->twilio['accountAuthtoken']);
    }

    public function dashboard()
    {
        $activities = $this->client->taskrouter->v1->workspaces($this->twilio['workspaceSid'])
                                     ->activities
                                     ->read();
        $capability = new WorkerCapability($this->twilio['accountSid'], $this->twilio['accountAuthtoken'], $this->twilio['workspaceSid'], 'WKfdbbee8fc1f08d75def2e5bdeab2a2cf');
        $capability->allowFetchSubresources();
        $capability->allowActivityUpdates();
        $capability->allowReservationUpdates();
        $token = $capability->generateToken(28800);
        return view('dashboard', ['worker_token'=>$token,'activities'=>$activities]);
    }
}
