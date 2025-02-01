<template>
  <div>
    <h2>Liste des tâches</h2>
    <TaskForm @task-added="fetchTasks" />
    <ul>
      <TaskItem 
        v-for="task in tasks" 
        :key="task.id" 
        :task="task" 
        @task-deleted="fetchTasks"
        @task-updated="fetchTasks" 
      />
    </ul>
  </div>
</template>

<script>
import TaskItem from "./TaskItem.vue";
import TaskForm from "./TaskForm.vue";
import taskService from "../services/taskService";

export default {
  components: { TaskItem, TaskForm },
  data() {
    return { tasks: [] };
  },
  async created() {
    await this.fetchTasks();
  },
  methods: {
    async fetchTasks() {
      this.tasks = (await taskService.getTasks()).data;
    }
  }
};
</script>
