import Vue from "vue";
import App from "./App";

Vue.prototype.t = window.t;
Vue.prototype.n = window.n;
Vue.prototype.OC = window.OC;
Vue.prototype.OCA = window.OCA;

new Vue({
  render: h => h(App)
}).$mount("#app");
