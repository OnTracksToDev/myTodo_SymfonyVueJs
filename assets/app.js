import { createApp } from 'vue';
import axios from 'axios';

const app = createApp({
    data() {
        return {
            tasks: [], 
            newTask: {
                title: '',
                description: ''
            }
        };
    },
    methods: {
        // Récupère tâches
        fetchTasks() {
            axios.get('/api/tasks')
                .then(response => {
                    this.tasks = response.data;
                    console.log(this.tasks); 
                })
                .catch(error => {
                    console.error('Error fetching tasks:', error);
                });
        },
    },
    mounted() {
        this.fetchTasks(); 
    }
});

app.mount('#app');