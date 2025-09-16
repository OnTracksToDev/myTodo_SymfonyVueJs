<template>
  <li :class="{ completed: task.isCompleted }">
    <!-- Case -->
    <input type="checkbox" :checked="task.isCompleted" @change="toggleCompletion" />
<!-- Titre -->
    <span v-if="!isEditingTitle" @dblclick="startEditing('title')">{{ task.title }}</span>
    <input 
      v-else 
      v-model="editTitle" 
      @blur="saveTask" 
      @keyup.enter="saveTask" 
      autofocus
    />

    - 

    <!-- Description -->
    <span v-if="!isEditingDescription" @dblclick="startEditing('description')">
      {{ task.description || 'Pas de description' }}
    </span>
    <textarea 
      v-else 
      v-model="editDescription" 
      @blur="saveTask" 
      @keyup.enter="saveTask"
    />
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
      isEditingTitle: false,
      isEditingDescription: false,
      editTitle: this.task.title,
      editDescription: this.task.description || "",
      isEditing: false
    };
  },
  methods: {
    // Edition classique
    editTask() {
      this.isEditing = true;
    },
    cancelEdit() {
      this.isEditing = false;
      this.editTitle = this.task.title;
      this.editDescription = this.task.description || "";
      this.isEditingTitle = false;
      this.isEditingDescription = false;
    },

    // Début édition rapide
    startEditing(field) {
      if (field === "title") this.isEditingTitle = true;
      if (field === "description") this.isEditingDescription = true;
    },

    // Sauvegarde (auto-save)
    async saveTask() {
      const updatedTask = {
        title: this.editTitle,
        description: this.editDescription,
        isCompleted: this.task.isCompleted
      };
      try {
        const response = await taskService.updateTask(this.task.id, updatedTask);
        this.isEditing = false;
        this.isEditingTitle = false;
        this.isEditingDescription = false;
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
