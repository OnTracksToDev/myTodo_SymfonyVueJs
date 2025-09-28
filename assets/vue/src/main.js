// CSS
import 'bootstrap/dist/css/bootstrap.min.css';
import '../styles/app.scss';

// Vue
import { createApp } from 'vue';
import App from './App.vue';

console.log("Coucou Vue.js !"); // Test

const app = createApp(App);
// Directive globale pour autofocus
app.directive('focus', {
  mounted(el) {
    el.focus();
  }
});
app.mount('#app');

// JS Bootstrap
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
