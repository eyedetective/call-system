<template>
  <div class="row">
      <div class="col-md-4 order-md-2 mb-4">
        <div class="card">
          <h5 class="card-header">
            Make a call
          </h5>
          <div class="card-body">
            <div class="form-group row">
              <label for="call-status" class="col-3 col-form-label">Status</label>
              <div class="col-9">
                <input id="call-status" class="form-control" type="text" placeholder="Connecting to .." readonly>
              </div>
            </div>
            <button class="btn btn-lg btn-success answer-button" disabled>Answer call</button>
            <button class="btn btn-lg btn-danger hangup-button" disabled onclick="hangUp()">Hang up</button>
          </div>
        </div>
      </div>

      <div class="col-md-8 order-md-1">
        <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Call Status</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Topic</th>
                    <th>Status</th>
                    <th>Call Datetime</th>
                </tr>
            </thead>
        </table>
      </div>

  </div>
</template>

<script>
const {Device} = require("twilio-client");
import $ from "jquery";
import 'vue-tel-input/dist/vue-tel-input.css';
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
export default {
    mounted() {
        $('#example').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": '/api/ticket',
            "columns": [
                { "data": "call_status" },
                { "data": "customer_name" },
                { "data": "customer_phone" },
                { "data": "topic" },
                { "data": "status" },
                { "data": "created_at" },
            ]
        });
        this.setupToken();
    },
    methods: {
        setupToken(){
            axios
                .post('/token',{forPage: window.location.pathname,_token: document.querySelector('meta[name="csrf-token"]').attr('content')})
                .then((response) => {
                    if (response.data.token) {
                        Device.setup(response.data.token);
                    }
                });
        },
        setupHandlers(){
            var _this = this;
            /* Report any errors to the call status display */
            Device.on('error', function (error) {
                console.log(error);
                if(error.code.match('321.*')){_this.setupToken();}
                _this.call.status="ERROR: " + error.message;
            });
            Device.on('ready', function (_device) {
                _this.call.status="Ready";
            });
            /* Callback for when Twilio Client initiates a new connection */
            Device.on('connect', function (connection) {
                // Enable the hang up button and disable the call buttons
                _this.call.hangupBtn=false;
                _this.call.anwserBtn=true;

                // If phoneNumber is part of the connection, this is a call from a
                // support agent to a customer's phone
                if ("phoneNumber" in connection.message) {
                    _this.call.status="In call with " + connection.message.phoneNumber;
                } else {
                    // This is a call from a website user to a support agent
                    _this.call.status="In call with support";
                }
            });
            /* Callback for when Twilio Client receives a new incoming call */
            Device.on('incoming', function(connection) {
                console.log(connection);
                _this.call.status("Incoming support call");

                // Set a callback to be executed when the connection is accepted
                connection.accept(function() {
                    _this.call.status("In call with customer");
                });

                // Set a callback on the answer button and enable it
                // answerButton.click(function() {
                //     connection.accept();
                // });
                // answerButton.prop("disabled", false);
            });

            Device.on('cancel', function(connection){
                console.log('cancel',connection);
            });

            /* Callback for when a call ends */
            Device.on('disconnect', function(connection) {
                // Disable the hangup button and enable the call buttons
                _this.call.hangupBtn=true;
                _this.call.anwserBtn=false;
                _this.call.status="Ready";
            });
        },
        acceptCall() {
            Device.accept()
        },
        hangup() {
            Twilio.Device.disconnectAll();
        }
    }
};
</script>

<style>
</style>
