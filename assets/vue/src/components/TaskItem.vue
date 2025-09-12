<template>
  <li :class="{ completed: task.isCompleted }">
    <!-- Case -->
    <input type="checkbox" :checked="task.isCompleted" @change="toggleCompletion" />
    <span>{{ task.title }}</span> - 
    <span>{{ task.description || 'Pas de description' }}</span>
    <button @click="editTask">Modifier</button>
    <button @click="deleteTask">Supprimer</button>

    <!-- Edition titre / description -->
    <div v-if="isEditing">
      <input v-model="editTitle" placeholder="Titre de la tâche" />
      <textarea v-model="editDescription" placeholder="Description"></textarea>
      <button @click="saveTask">Sauvegarder</button>
      <button @click="cancelEdit">Annuler</button>
    </div>
  </li>
</template>

<script>
import taskService from "../services/taskService";

export default {
  props: { task: Object },
  data() {
    return {
      isEditing: false,
      editTitle: this.task.title,
      editDescription: this.task.description || ""
    };
  },
  methods: {
    editTask() {
      this.isEditing = true;
    },
    cancelEdit() {
      this.isEditing = false;
      this.editTitle = this.task.title;
      this.editDescription = this.task.description || "";
    },
    async saveTask() {
      const updatedTask = {
        title: this.editTitle,
        description: this.editDescription,
        isCompleted: this.task.isCompleted
      };
      try {
        const response = await taskService.updateTask(this.task.id, updatedTask);
        this.isEditing = false;
        this.$emit("task-updated", response.data);
      } catch (error) {
        console.error("Erreur lors de la mise à jour de la tâche :", error);
      }
    },
    async deleteTask() {
      try {
        await taskService.deleteTask(this.task.id);
        this.$emit("task-deleted", this.task.id);
      } catch (error) {
        console.error("Erreur lors de la suppression de la tâche :", error);
      }
    },
    async toggleCompletion() {
      const updatedTask = { ...this.task, isCompleted: !this.task.isCompleted };
      try {
        const response = await taskService.updateTask(this.task.id, updatedTask);
        this.$emit("task-updated", response.data);
      } catch (error) {
        console.error("Erreur lors de la mise à jour du statut :", error);
      }
    }
  }
};
</script>

<style scoped>
.completed {
  text-decoration: line-through;
  color: #888;
  background-color: #f0f0f0;
}
li {
  padding: 5px 0;
}
</style>
