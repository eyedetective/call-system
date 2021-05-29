<template>
    <div id="widget-customer-call">
        <div id="widget-browser-call" class="">
            <button id="conversion-btn" aria-label="browser call" @click="popup=true">&phone;</button>
        </div>
        <div id="popup-reg" :class="['popup', [popup?'active':'']]">
            <div class="popup-content">
                <ul class="tabs-nav">
                    <li @click="tab=1" :class="[tab==1?'active':'']">ต้องการช่วยเหลือด่วน</li>
                    <li @click="tab=2" :class="[tab==2?'active':'']">ให้ติดต่อกลับภายหลัง</li>
                </ul>
                <div class="tabs-stage">
                        <div class="event-header" >{{ tab==1 ? 'ต้องการช่วยเหลือด่วน': 'ให้ติดต่อกลับภายหลัง'}}</div>
                        <div id="send" class="send-form">
                            <div class="form-group">
                                <select v-model="form.topic" class="form-control">
                                    <option value="">-- เรื่องที่ต้องการติดต่อ --</option>
                                    <option v-for="topic in list.topic" :key="'topic'+topic.value" :value="topic.value">{{topic.name}}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="กรอกชื่อ..." v-model="form.customer_name">
                            </div>
                            <div class="form-group">
                                <vue-tel-input class="form-control" v-model="form.customer_phone" placeholder="กรอกเบอร์โทร"
                                    @input="setInputTel" default-country="th" :dropdown-options="tel.options"></vue-tel-input>
                            </div>
                            <div class="form-group lui-flex" v-show="tab==2">
                                <select v-model="form.schedule_date" class="form-control">
                                    <option v-for="date in list.date" :key="'date'+date" :value="date | fdate">{{date|shortDate}}</option>
                                </select>
                                <select v-model="form.schedule_time" class="form-control">
                                    <option v-for="t in list.time" :key="'time'+t" :value="t">{{t}}</option>
                                </select>
                            </div>
                            <button v-show="tab==1 && !call.callBtn" @click="callSupport()" class="main-btn-call" :disabled="!validForm">โทรทันที</button>
                            <button v-show="tab==2 && !call.callBtn" @click="scheduleCall()" class="main-btn-call" :disabled="!validForm">โทรกลับมานะ</button>
                            <button v-show="!call.hangupBtn" @click="hangup()" class="main-btn-hangup">วางสาย</button>
                    </div>
                </div>
                <span @click="popup=false" class="fade-out main-btn-circle">&times;</span>
            </div>
        </div>
    </div>
</template>

<script>
const Twilio = require("twilio-client");
import { VueTelInput } from 'vue-tel-input'
import 'vue-tel-input/dist/vue-tel-input.css';
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
export default {
    components:{VueTelInput},
    data() {
        return {
            call : {
                status : 'waiting..',
                callBtn : false,
                hangupBtn : true,
            },
            tel: {
                options: {
                    showDialCodeInList:true,
                    showDialCodeInSelection:true,
                    showFlags:true,
                    tabindex:0,
                }
            },
            tab: 1,
            list: {
                topic:[{name:'ไอที',value:'IT'},{name:'โปรแกรม',value:'Programmer'}],
                date:[],
                time:[],
            },
            popup : false,
            form : {
                topic : '',
                customer_name : '',
                customer_phone : '',
                schedule_date : null,
                schedule_time : null,
                utm_source : '',
                utm_medium : '',
                utm_content : '',
                utm_campaign : '',
                utm_term : '',
                source : '',
            },
            validPhone: false,
        }
    },
    filters: {
        shortDate(date){
            return date.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
            })
        },
        fdate(d){
            var month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;
            return [year, month, day].join('-');
        }
    },
    computed: {
        validForm(){
            return this.form.customer_name && this.form.customer_phone && this.form.topic && this.validPhone;
        }
    },
    mounted(){
        this.list.time = Array.from({length: 9}, function(x, i) {return (i ? (i+9) : '0'+(i+9)) +':00';});
        var today = new Date();
        this.list.date = Array.from({length: 4}, function(x, i){return new Date(today.setDate(today.getDate()+1))});
        var month = '' + (this.list.date[0].getMonth() + 1),
                day = '' + this.list.date[0].getDate(),
                year = this.list.date[0].getFullYear();
        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;
        this.form.schedule_date = [year, month, day].join('-');
        this.form.schedule_time = this.list.time[0];
        this.setupToken();
        this.setupHandlers();
        const urlParams = new URLSearchParams(window.location.search);
        this.form.utm_source = urlParams.get('utm_source') || '';
        this.form.utm_medium = urlParams.get('utm_medium') || '';
        this.form.utm_content = urlParams.get('utm_content') || '';
        this.form.utm_campaign = urlParams.get('utm_campaign') || '';
        this.form.utm_term = urlParams.get('utm_term') || '';
        this.form.source = window.location.hostname;
    },
    methods : {
        setInputTel(number,phone){
            this.form.customer_phone = phone.number;
            this.validPhone = phone.valid;
        },
        setupHandlers(){
            var _this = this;
            /* Report any errors to the call status display */
            Twilio.Device.on('error', function (error) {
                console.error(error);
                if(String(error.code).match(/312.*/)){_this.setupToken();}
                _this.call.status="ERROR: " + error.message;
            });
            Twilio.Device.on('ready', function (_device) {
                _this.call.status="Ready";
            });
            /* Callback for when Twilio Client initiates a new connection */
            Twilio.Device.on('connect', function (connection) {
                // Enable the hang up button and disable the call buttons
                _this.call.hangupBtn=false;
                _this.call.callBtn=true;

                // If phoneNumber is part of the connection, this is a call from a
                // support agent to a customer's phone
                if ("phoneNumber" in connection.message) {
                    _this.call.status="In call with " + connection.message.phoneNumber;
                } else {
                    // This is a call from a website user to a support agent
                    _this.call.status="In call with support";
                }
            });

            /* Callback for when a call ends */
            Twilio.Device.on('disconnect', function(connection) {
                // Disable the hangup button and enable the call buttons
                _this.call.hangupBtn=true;
                _this.call.callBtn=false;

                _this.call.status="Ready";
            });
        },
        setupToken(){
            axios
                .post('/api/call/token')
                .then((response) => {
                    if (response.data.token) {
                        Twilio.Device.setup(response.data.token);
                    }
                });
        },
        callSupport() {
            var resource = this.form;
            resource.widget = true;
            Twilio.Device.connect(resource);
        },
        scheduleCall() {
             axios
                .post('/api/call/schedule', this.form)
                .then((response) => {
                    if (response.data.status) {
                        alert('done');
                    }
                });
        },
        hangup() {
            Twilio.Device.disconnectAll();
        }
    }
}
</script>
