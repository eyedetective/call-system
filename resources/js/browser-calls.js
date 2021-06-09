import axios from "axios";
import $ from "jquery";
const { Device } = require('twilio-client');
if(document.querySelector('meta[name=api-token]')){
    window.apitoken =document.querySelector('meta[name=api-token]').getAttribute('content')
    axios.defaults.headers.common['Authorization'] = 'Bearer '+window.apitoken;
    axios.defaults.headers.common['Accept'] = 'application/json';
}
/**
 * Twilio Client configuration for the browser-calls-laravel
 * example application.
 */

// Store some selectors for elements we'll reuse
var callStatus = $("#call-status");
var answerButton = $(".answer-button");
var hangUpButton = $(".hangup-button");

var device = null,device_conn = null,mic_status = true;

/* Helper function to update the call status bar */
function updateCallStatus(status) {
    callStatus.attr('placeholder', status);
}

/* Get a Twilio Client token with an AJAX request */

function setupHandlers(device) {
    device.on('ready', function (_device) {
        updateCallStatus("Ready");
    });

    /* Report any errors to the call status display */
    device.on('error', function (error) {
        console.log(error);
        if(String(error.code).match(/31205/)){setupClient();}
        updateCallStatus("ERROR: " + error.message);
    });

    /* Callback for when Twilio Client initiates a new connection */
    device.on('connect', function (connection) {
        device_conn = connection;
        device_conn.mute(mic_status);
        // Enable the hang up button and disable the call buttons
        hangUpButton.prop("disabled", false);
        answerButton.prop("disabled", true);
    });

    /* Callback for when a call ends */
    device.on('disconnect', function(connection) {
        device_conn = null;
        // Disable the hangup button and enable the call buttons
        document.querySelector('.caller-info').classList.add('d-none');
        document.getElementById('caller-name').value = '';
        document.getElementById('caller-phone').value = '';
        document.getElementById('call-topic').value = '';
        hangUpButton.prop("disabled", true);
        answerButton.prop("disabled", true);
        updateCallStatus("Ready");
    });

    /* Callback for when Twilio Client receives a new incoming call */
    device.on('incoming', function(connection) {
        updateCallStatus("Incoming support call");
        var info = connection.customParameters;
        document.querySelector('.caller-info').classList.remove('d-none');
        document.getElementById('caller-name').value = info.get('customer_name');
        document.getElementById('caller-phone').value = info.get('customer_phone');
        document.getElementById('call-topic').value = info.get('topic');
        window.refreshTable()

        // Set a callback to be executed when the connection is accepted
        connection.accept(function() {
            updateCallStatus("In call with customer");
            axios
                .post('/api/ticket/'+info.get('tid'),{_method: 'PUT',call_status:'Answered',outgoingCall:connection.parameters.CallSid})
                .then((response) => {
                    window.refreshTable()
                }).catch((error)=>console.log("Could not get a token from server!"));
        });

        // Set a callback on the answer button and enable it
        answerButton.on('click',function() {
            connection.accept();
        });
        answerButton.prop("disabled", false);
    });

    device.on('cancel', function(connection){
        document.querySelector('.caller-info').classList.add('d-none');
        document.getElementById('caller-name').value = '';
        document.getElementById('caller-phone').value = '';
        document.getElementById('call-topic').value = '';
        hangUpButton.prop("disabled", true);
        answerButton.prop("disabled", true);
    });
};
window.toggleMute = function(el){
    if(device_conn){
        device_conn.mute(mic_status);
        if(mic_status){
            el.innerHTML = '<i class="fas fa-microphone-slash"></i>';
            mic_status = false;
        }else{
            el.innerHTML = '<i class="fas fa-microphone"></i>';
            mic_status = true;
        }
    }
}

window.setupOnline = function() {
    var now = new Date(),start = new Date(),end = new Date();
    var week = ['sunday','monday','tuesday','wednesday','thursday','saturday']
    var day = week[now.getDay()];
    if(me[day+'_start']){
        let start_time = me[day+'_start'].split(':');
        start.setHours(start_time[0],start_time[1],0)
    }
    if(me[day+'_end']){
        let end_time = me[day+'_end'].split(':');
        end.setHours(end_time[0],end_time[1],0)
    }
    if(start < now && end > now){
        if ('permissions' in navigator) {
            var noop = function () {};
            navigator.getUserMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);
            try {
                navigator.permissions.query({name: 'microphone'})
                    .then(function (permission) {
                        if(permission.state != 'granted'){
                            document.getElementById('btnStatus').checked = false;
                            alert('Please allow microphone before online')
                            navigator.getUserMedia({audio: true}, function(){setupClient();}, noop)
                        }else{
                            setupClient();
                        }
                        permission.addEventListener('change', function (e) {
                            if(e.state != 'granted'){setupOffline();}
                        });
                    });
                } catch (e) {}
        }else{
            setupClient();
        }
    }else{
        document.getElementById('btnStatus').checked = false;
        alert('คุณอยู่นอกเหนือเวลาทำการ ให้ติดต่อแอดมิน');
    }
}
window.setupStatus = function(el) {
    if(el.checked){
        setupOnline()
    }else{
        setupOffline()
    }
}

window.setupClient = function() {
    document.getElementById('btnStatus').checked = true;
    document.getElementById('txt-status').innerHTML = "Online";
    updateCallStatus('Connecting to Twilio...');
    axios
        .post('/token',{forPage: window.location.pathname,_token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')})
        .then((response) => {
            device = new Device();
            device.setup(response.data.token);
            setupHandlers(device);
            axios
            .post('/api/user/available',{is_available: 1});
        }).catch((error)=>console.log("Could not get a token from server!"));
};

window.setupOffline = function() {
    axios
        .post('/api/user/available',{is_available: 0});
    updateCallStatus('Disconnect')
    document.getElementById('btnStatus').checked = false;
    document.getElementById('txt-status').innerHTML = "Offline";
    if(device) device.destroy();
};

window.hangUp = function() {
    document.querySelector('.caller-info').classList.add('d-none');
    document.getElementById('caller-name').value = '';
    document.getElementById('caller-phone').value = '';
    document.getElementById('call-topic').value = '';
    device.disconnectAll();
};

window.updateTicketField = function(id,type,el) {
    var resource = {_method:'PUT'};
    resource[type] = el.value;
    axios
        .post('/api/ticket/'+id,resource)
        .then((response) => {
            console.log('done');
        }).catch((error)=>console.log(error));
}
window.updateTicket = function() {
    var $form = document.forms['cdetail-form']
    var id = $form.querySelector('[name=id]').value
    var resource = {_method:'PUT'};
    resource['status'] = $form.querySelector('[name=status]').value;
    resource['comment'] = $form.querySelector('[name=comment]').value;
    resource['assign_user_id'] = $form.querySelector('[name=assign_user_id]').value;
    axios
        .post('/api/ticket/'+id,resource)
        .then((response) => {
            alert('done');
            window.refreshTable();
        }).catch((error)=>console.log(error));
}

window.setCallDetail = function(id){
    document.querySelector('.ui--loader').style.display ='';
    document.querySelector('.ui--control-overlay').style.display ='';
    axios
        .get('/api/ticket/'+id)
        .then((response) => {
            var ticket = response.data;
            document.getElementById('cdetail-id').value = ticket.id;
            document.getElementById('cdetail-call-status').value = ticket.call_status;
            document.getElementById('cdetail-topic').value = ticket.topic.name;
            document.getElementById('cdetail-customer-phone').value = ticket.customer_phone;
            document.getElementById('cdetail-customer-name').value = ticket.customer_name;
            document.getElementById('cdetail-customer-name').value = ticket.customer_name;
            document.getElementById('cdetail-call-rep').value = ticket.rep_by ? ticket.rep_by.username : '';
            var d = new Date(ticket.created_at);
            document.getElementById('cdetail-date').value = d.toLocaleDateString('th-TH');
            document.getElementById('cdetail-time').value = d.toLocaleTimeString('th-TH');
            document.getElementById('cdetail-duration').value = ticket.call_durations;
            document.getElementById('cdetail-source').value = ticket.source;
            document.getElementById('cdetail-ip-address').value = ticket.ip;
            document.getElementById('cdetail-utm-source').value = ticket.utm_source;
            document.getElementById('cdetail-utm-medium').value = ticket.utm_medium;
            document.getElementById('cdetail-utm-campaign').value = ticket.utm_campaign;
            document.getElementById('cdetail-utm-content').value = ticket.utm_content;
            document.getElementById('cdetail-utm-term').value = ticket.utm_term;
            document.getElementById('cdetail-comment').value = ticket.comment;
            document.getElementById('cdetail-assign').value = ticket.assign_user_id;
            document.getElementById('cdetail-status').value = ticket.status;
            document.querySelector('.ui--loader').style.display ='none';
        }).catch((error)=>console.log(error));
}
