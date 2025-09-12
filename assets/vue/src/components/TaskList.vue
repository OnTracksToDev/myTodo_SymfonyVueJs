<template>
  <div>
    <h2>Liste des tâches</h2>
    <TaskForm @task-added="addTaskToList" />
    <ul>
      <TaskItem 
        v-for="task in tasks" 
        :key="task.id" 
        :task="task" 
        @task-deleted="removeTaskFromList"
        @task-updated="updateTaskInList" 
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
    const response = await taskService.getTasks();
    this.tasks = response.data;
  },
  methods: {
    addTaskToList(task) {
      this.tasks.push(task);
    },
    removeTaskFromList(taskId) {
      this.tasks = this.tasks.filter(t => t.id !== taskId);
    },
    updateTaskInList(updatedTask) {
      const index = this.tasks.findIndex(t => t.id === updatedTask.id);
      if (index !== -1) {
        this.tasks.splice(index, 1, updatedTask);
      }
    }
  }
};
</script>
