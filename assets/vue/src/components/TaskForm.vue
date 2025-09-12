<template>
  <form @submit.prevent="addTask">
    <input v-model="taskTitle" placeholder="Nouvelle tâche" required />
    <button type="submit">Ajouter</button>
  </form>
</template>

<script>
import taskService from "../services/taskService";

export default {
  data() {
    return { taskTitle: "" };
  },
  methods: {
    async addTask() {
      if (!this.taskTitle.trim()) return;

      const newTask = {
        title: this.taskTitle,
        description: "",
        isCompleted: false
      };

      try {
        const response = await taskService.createTask(newTask);
        this.taskTitle = "";
        this.$emit("task-added", response.data);
      } catch (error) {
        console.error("Erreur lors de l'ajout de la tâche:", error);
      }
    }
  }
};
</script>
