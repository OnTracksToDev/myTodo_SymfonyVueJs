<template>
  <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start mb-2"
      :class="{ 'bg-light text-decoration-line-through': task.isCompleted }">

    <!-- Case -->
    <div class="d-flex flex-column flex-grow-1">
      <div class="d-flex align-items-center mb-1">
        <input type="checkbox" class="form-check-input me-2" :checked="task.isCompleted" @change="toggleCompletion" />

        <!-- Titre -->
        <span v-if="!isEditingTitle" @dblclick="startEditing('title')" class="fw-bold">
          {{ task.title }}
        </span>
        <input 
          v-else 
          v-model="editTitle" 
          @blur="saveTask" 
          @keyup.enter="saveTask" 
          class="form-control form-control-sm me-2"
          autofocus
        />
      </div>

      <!-- Description -->
      <div>
        <span v-if="!isEditingDescription" @dblclick="startEditing('description')" class="text-muted">
          {{ task.description || 'Pas de description' }}
        </span>
        <textarea 
          v-else 
          v-model="editDescription" 
          @blur="saveTask" 
          @keyup.enter="saveTask" 
          class="form-control form-control-sm mt-1"
        />
      </div>
    </div>

    <div class="mt-2 mt-md-0 d-flex gap-1">
      <button v-if="!isEditing" class="btn btn-sm btn-outline-secondary" @click="editTask">Modifier</button>
      <button v-if="!isEditing" class="btn btn-sm btn-outline-danger" @click="deleteTask">Supprimer</button>

      <!-- Edition titre / description -->
      <div v-if="isEditing" class="d-flex flex-column gap-1">
        <input v-model="editTitle" class="form-control form-control-sm" placeholder="Titre de la tâche" />
        <textarea v-model="editDescription" class="form-control form-control-sm" placeholder="Description"></textarea>
        <div class="d-flex gap-1">
          <button class="btn btn-sm btn-success" @click="saveTask">Sauvegarder</button>
          <button class="btn btn-sm btn-secondary" @click="cancelEdit">Annuler</button>
        </div>
      </div>
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

    editTask() { this.isEditing = true; },
    cancelEdit() {
      this.isEditing = false;
      this.isEditingTitle = false;
      this.isEditingDescription = false;
      this.editTitle = this.task.title;
      this.editDescription = this.task.description || "";
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
.list-group-item {
  transition: background-color 0.2s;
}
</style>
