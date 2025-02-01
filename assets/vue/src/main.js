import { createApp } from 'vue';
import App from './App.vue';
import '../styles/app.scss';


console.log("Coucou Vue.js !"); // Test

const app = createApp(App);
app.mount('#app');
