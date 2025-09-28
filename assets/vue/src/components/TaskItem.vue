<template>
  <li :class="['list-group-item', { completed: task.isCompleted }]">
    <div v-if="!isDeleting">
      <div class="d-flex align-items-start justify-content-between">
        <!-- Case -->
        <div class="d-flex align-items-center">
          <input
            type="checkbox"
            class="form-check-input me-2"
            :checked="task.isCompleted"
            @change="toggleCompletion"
          />

          <!-- Titre -->
          <span
            v-if="!isEditingTitle"
            @dblclick="startEditing('title')"
            class="editable d-inline-flex align-items-center position-relative"
          >
            {{ task.title }}
            <i class="bi bi-pencil-fill edit-icon ms-1"></i>
          </span>
          <input
            v-else
            v-model="editTitle"
            @blur="saveTask"
            @keyup.enter="saveTask"
            class="form-control form-control-sm"
            v-focus
          />
        </div>

        <!-- Bouton supprimer -->
        <button @click="deleteTask" class="btn btn-outline-danger btn-sm ms-2">
          Supprimer
        </button>
      </div>

      <!-- Description -->
      <div class="mt-2">
        <span
          v-if="!isEditingDescription"
          @dblclick="startEditing('description')"
          :class="[
            'editable d-inline-flex align-items-center position-relative',
            { 'text-muted fst-italic': !task.description },
          ]"
        >
          {{ task.description || "Pas de description" }}
          <i class="bi bi-pencil-fill edit-icon ms-1"></i>
        </span>
        <textarea
          v-else
          v-model="editDescription"
          @blur="saveTask"
          @keyup.enter="saveTask"
          rows="2"
          class="form-control form-control-sm"
          placeholder="Saisissez une description..."
          v-focus
        ></textarea>
      </div>

      <!-- Feedback sauvegarde -->
      <div v-if="isSaving" class="text-info small mt-2">
        <span class="spinner-border spinner-border-sm me-1"></span>
        Sauvegarde...
      </div>
      <div v-if="saveSuccess" class="text-success small mt-2">
        ✔ Sauvegardé !
      </div>
    </div>

    <!-- Feedback suppression -->
    <div v-else class="text-danger small">❌ Tâche supprimée...</div>
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
      editDescription: this.task.description,
      isSaving: false,
      saveSuccess: false,
      isDeleting: false,
    };
  },
  methods: {
    startEditing(field) {
      if (field === "title") this.isEditingTitle = true;
      if (field === "description") this.isEditingDescription = true;
    },

    async saveTask() {
      if (this.isSaving) return;
      this.isSaving = true;

      const updatedTask = {
        title: this.editTitle,
        description: this.editDescription.trim() || "",
        isCompleted: this.task.isCompleted,
      };

      try {
        const start = Date.now();
        const response = await taskService.updateTask(
          this.task.id,
          updatedTask
        );

        const elapsed = Date.now() - start;
        const minDuration = 400;

        setTimeout(() => {
          this.isEditingTitle = false;
          this.isEditingDescription = false;
          this.$emit("task-updated", response.data);

          this.saveSuccess = true;
          setTimeout(() => (this.saveSuccess = false), 1500);
          this.isSaving = false;
        }, Math.max(0, minDuration - elapsed));
      } catch (error) {
        console.error("Erreur update :", error);
        this.isSaving = false;
      }
    },

    async deleteTask() {
      this.isDeleting = true;
      try {
        await taskService.deleteTask(this.task.id);

        // Attendre 1 seconde pour afficher le feedback
        setTimeout(() => {
          this.$emit("task-deleted", this.task.id);
        }, 1000);
      } catch (error) {
        console.error("Erreur suppression :", error);
        this.isDeleting = false;
      }
    },

    async toggleCompletion() {
      const updatedTask = { ...this.task, isCompleted: !this.task.isCompleted };
      try {
        const response = await taskService.updateTask(
          this.task.id,
          updatedTask
        );
        this.$emit("task-updated", response.data);
      } catch (error) {
        console.error("Erreur statut :", error);
      }
    },
  },
};
</script>

<style scoped>
.completed {
  color: #888;
  background-color: #f0f0f0;
}

/* Curseur + hover */
.editable {
  cursor: text;
  transition: background-color 0.2s;
  padding-right: 0.5rem;  /* espace pour icône */
}

.editable:hover {
  background-color: #f8f9fa;
}

/* Icône crayon */
.edit-icon {
  font-size: 0.8rem;
  color: #6c757d;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s;
}

.editable:hover .edit-icon {
  opacity: 1;
}

/* Feedback */
.text-info,
.text-success {
  transition: opacity 0.3s ease;
}
</style>