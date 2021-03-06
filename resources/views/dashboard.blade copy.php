@extends('layouts.main')
@section('title', 'Voice Agent Screen')
@section('head_last')
<link rel="stylesheet" href="//media.twiliocdn.com/taskrouter/quickstart/agent.css"/>
<style>body{padding: 0;}</style>
@endsection
@section('content')
    <div class="content">
        <section class="agent-activity offline">
            <p class="activity">Offline</p>
            <button class="change-activity" data-next-activity="Available">Go Available</button>
        </section>
        <section class="agent-activity available">
            <p class="activity"><span>Available</span></p>
            <button class="change-activity" data-next-activity="Offline">Go Offline</button>
        </section>
        <section class="agent-activity reserved">
            <p class="activity">Reserved</p>
        </section>
        <section class="agent-activity busy">
            <p class="activity">Busy</p>
        </section>
        <section class="agent-activity wrapup">
            <p class="activity">Wrap-Up</p>
            <button class="change-activity" data-next-activity="Available">Go Available</button>
            <button class="change-activity" data-next-activity="Offline">Go Offline</button>
        </section>
        <section class="log">
        <textarea id="log" readonly="true"></textarea>
        </section>
    </div>
@endsection
@section('afterbody')
<script src="https://sdk.twilio.com/js/taskrouter/v1.21/taskrouter.min.js" integrity="sha384-5fq+0qjayReAreRyHy38VpD3Gr9R2OYIzonwIkoGI4M9dhfKW6RWeRnZjfwSrpN8" crossorigin="anonymous"></script>
  <script>
    window.workerToken = "{{$worker_token}}";
    function registerTaskRouterCallbacks() {
        worker.on('ready', function(worker) {
            agentActivityChanged(worker.activityName);
            logger("Successfully registered as: " + worker.friendlyName)
            logger("Current activity is: " + worker.activityName);
        });

        worker.on('activity.update', function(worker) {
            agentActivityChanged(worker.activityName);
            logger("Worker activity changed to: " + worker.activityName);
        });

        worker.on("reservation.created", function(reservation) {
            logger("-----");
            logger("You have been reserved to handle a call!");
            logger("Call from: " + reservation.task.attributes.from);
            logger("Selected language: " + reservation.task.attributes.selected_language);
            logger("-----");
        });

        worker.on("reservation.accepted", function(reservation) {
            logger("Reservation " + reservation.sid + " accepted!");
        });

        worker.on("reservation.rejected", function(reservation) {
            logger("Reservation " + reservation.sid + " rejected!");
        });

        worker.on("reservation.timeout", function(reservation) {
            logger("Reservation " + reservation.sid + " timed out!");
        });

        worker.on("reservation.canceled", function(reservation) {
            logger("Reservation " + reservation.sid + " canceled!");
        });
    }

        /* Hook up the agent Activity buttons to Worker.js */

    function bindAgentActivityButtons() {
        // Fetch the full list of available Activities from TaskRouter. Store each
        // ActivitySid against the matching Friendly Name
        var activitySids = {};
        worker.activities.fetch(function(error, activityList) {
            var activities = activityList.data;
            var i = activities.length;
            while (i--) {
            activitySids[activities[i].friendlyName] = activities[i].sid;
            }
        });

        /* For each button of class 'change-activity' in our Agent UI, look up the
        ActivitySid corresponding to the Friendly Name in the button???s next-activity
        data attribute. Use Worker.js to transition the agent to that ActivitySid
        when the button is clicked.*/
        var elements = document.getElementsByClassName('change-activity');
        var i = elements.length;
        while (i--) {
            elements[i].onclick = function() {
                var nextActivity = this.dataset.nextActivity;
                var nextActivitySid = activitySids[nextActivity];
                console.log(this.dataset.nextActivity,activitySids);
                worker.update({"ActivitySid":nextActivitySid});
            }
        }
    }

        /* Update the UI to reflect a change in Activity */

    function agentActivityChanged(activity) {
        hideAgentActivities();
        showAgentActivity(activity);
    }

    function hideAgentActivities() {
        var elements = document.getElementsByClassName('agent-activity');
        var i = elements.length;
        while (i--) {
            elements[i].style.display = 'none';
        }
    }

    function showAgentActivity(activity) {
        activity = activity.toLowerCase();
        var elements = document.getElementsByClassName(('agent-activity ' + activity));
        elements.item(0).style.display = 'block';
    }

        /* Other stuff */

    function logger(message) {
        var log = document.getElementById('log');
        log.value += "\n> " + message;
        log.scrollTop = log.scrollHeight;
    }

        window.onload = function() {
            // Initialize TaskRouter.js on page load using window.workerToken -
            // a Twilio Capability token that was set in a &lt;script> in agent.php
            logger("Initializing...");
            window.worker = new Twilio.TaskRouter.Worker(workerToken);
            registerTaskRouterCallbacks();
            bindAgentActivityButtons();
        };
  </script>
@endsection
