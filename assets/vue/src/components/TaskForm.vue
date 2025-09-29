<template>
  <form @submit.prevent="addTask" class="row g-2 align-items-center mb-0">
    <!-- Champ texte -->
    <div class="col-12 col-md-8">
      <input
        v-model="taskTitle"
        type="text"
        class="form-control"
        placeholder="Nouvelle tâche"
        required
        @keyup.enter="addTask"
      />
    </div>

    <!-- Bouton -->
    <div class="col-12 col-md-4 d-grid">
      <button type="submit" class="btn btn-dark">Ajouter</button>
    </div>
  </form>

  <div ref="alertContainer"></div>
</template>
<script>
import taskService from "../services/taskService";

export default {
  emits: ["task-added"],
  data() {
    return {
      taskTitle: "",
    };
  },
  methods: {
    async addTask() {
      const title = this.taskTitle.trim();
      if (!title) return;

      const newTask = {
        title,
        description: "",
        isCompleted: false,
      };

      try {
        const response = await taskService.createTask(newTask);
        this.taskTitle = "";

        // Feedback visuel avec alert Bootstrap
        const alertDiv = document.createElement("div");
        alertDiv.className = "alert alert-success mt-2";
        alertDiv.textContent = `Tâche "${title}" ajoutée !`;
        this.$refs.alertContainer.appendChild(alertDiv);
        setTimeout(() => alertDiv.remove(), 2000);

        this.$emit("task-added", response.data);
      } catch (error) {
        console.error("Erreur lors de l'ajout de la tâche:", error);

        const alertDiv = document.createElement("div");
        alertDiv.className = "alert alert-danger mt-2";
        alertDiv.textContent = `Erreur : impossible d'ajouter la tâche.`;
        this.$refs.alertContainer.appendChild(alertDiv);
        setTimeout(() => alertDiv.remove(), 3000);
      }
    },
  },
};
</script>
<style scoped>
form {
  margin-bottom: 1rem;
}
</style>
