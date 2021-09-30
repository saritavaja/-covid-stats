import "./bootstrap";
import Vue from 'vue';
import router from "./plugins/router";
import apolloProvider from "./plugins/apollo";

String.prototype.toSeparatedNumber = function (x) {
    if (!x) {
        x = this;
    }

    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

const app = new Vue({
    el: '#app',
    router,
    apolloProvider
});
