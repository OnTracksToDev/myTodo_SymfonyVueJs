<template>
  <li>
    <div v-if="!isEditing">
      <span>{{ task.title }}</span> - 
      <span>{{ task.description || "Pas de description" }}</span> - 
      <span>{{ task.isCompleted ? "Complétée" : "En cours" }}</span>
      <button @click="editTask">Modifier</button>
      <button @click="deleteTask">Supprimer</button>
    </div>

    <div v-else>
      <input v-model="editTitle" placeholder="Titre de la tâche" />
      <textarea v-model="editDescription" placeholder="Description"></textarea>
      <label>
        <input type="checkbox" v-model="editIsCompleted" /> Complète
      </label>
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
      editDescription: this.task.description || "",
      editIsCompleted: this.task.isCompleted
    };
  },
  methods: {
    editTask() {
      this.isEditing = true;
    },
    cancelEdit() {
      this.isEditing = false;
      this.resetFields();
    },
    async saveTask() {
      const updatedTask = {
        title: this.editTitle,
        description: this.editDescription,
        isCompleted: this.editIsCompleted
      };
      try {
        const response = await taskService.updateTask(this.task.id, updatedTask);
        this.isEditing = false;
        this.$emit("task-updated", response.data);
      } catch (error) {
        console.error("Erreur lors de la mise à jour de la tâche:", error);
      }
    },
    async deleteTask() {
      try {
        await taskService.deleteTask(this.task.id);
        this.$emit("task-deleted", this.task.id);
      } catch (error) {
        console.error("Erreur lors de la suppression de la tâche:", error);
      }
    },
    resetFields() {
      this.editTitle = this.task.title;
      this.editDescription = this.task.description || "";
      this.editIsCompleted = this.task.isCompleted;
    }
  }
};
</script>
