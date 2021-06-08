var parent_widget = document.createElement('div');
parent_widget.setAttribute('id','container-call-widget');
var widget = document.createElement('call-browser-widget');
widget.setAttribute('domain',process.env.MIX_API_URL);
parent_widget.appendChild(widget)
document.body.appendChild(parent_widget)

import Vue from "vue";
Vue.component('callBrowserWidget', require('./widget/Widget.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const app = new Vue({
    el: '#container-call-widget',
});
