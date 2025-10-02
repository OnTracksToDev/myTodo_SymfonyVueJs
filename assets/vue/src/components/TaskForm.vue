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

        this.$emit("task-added", response.data);

      } catch (error) {
        console.error("Erreur lors de l'ajout de la tâche:", error);
        this.$emit("show-toast", `❌ Impossible d'ajouter la tâche`);
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
