<template>
  <form @submit.prevent="addTask" class="d-flex gap-2 align-items-center">
    <input 
      v-model="taskTitle" 
      type="text" 
      class="form-control" 
      placeholder="Nouvelle tâche" 
      required
      @keyup.enter="addTask"
    />

    <button type="submit" class="btn btn-primary">
      Ajouter
    </button>
  </form>
</template>

<script>
import taskService from "../services/taskService";

export default {
  data() {
    return {
      taskTitle: ""
    };
  },
  methods: {
    async addTask() {
      const title = this.taskTitle.trim();
      if (!title) return;

      const newTask = {
        title,
        description: "",
        isCompleted: false
      };

      try {
        const response = await taskService.createTask(newTask);
        this.taskTitle = "";

        // Feedback visuel avec alert Bootstrap
        const alertDiv = document.createElement("div");
        alertDiv.className = "alert alert-success mt-2";
        alertDiv.textContent = `Tâche "${title}" ajoutée !`;
        this.$el.appendChild(alertDiv);
        setTimeout(() => alertDiv.remove(), 2000);

        this.$emit("task-added", response.data);
      } catch (error) {
        console.error("Erreur lors de l'ajout de la tâche:", error);

        const alertDiv = document.createElement("div");
        alertDiv.className = "alert alert-danger mt-2";
        alertDiv.textContent = `Erreur : impossible d'ajouter la tâche.`;
        this.$el.appendChild(alertDiv);
        setTimeout(() => alertDiv.remove(), 3000);
      }
    }
  }
};
</script>
<style scoped>
form {
  margin-bottom: 1rem;
}
</style>