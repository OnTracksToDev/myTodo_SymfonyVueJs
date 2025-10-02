<template>
  <li
    :class="['list-group-item', { completed: task.isCompleted }]"
    class="position-relative d-flex align-items-center task-item"
  >
    <!-- Partie gauche : case à cocher -->
    <div class="d-flex align-items-center me-3">
      <input
        type="checkbox"
        class="custom-checkbox"
        :checked="task.isCompleted"
        @change="toggleCompletion"
      />
    </div>

    <!-- Partie centrale -->
    <div class="flex-grow-1 d-flex flex-column">
      <!-- Titre -->
      <div class="d-flex align-items-center">
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

      <!-- Description -->
      <div class="mt-1">
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
      <transition name="fade">
        <div v-if="saveSuccess" class="text-success fw-semibold">
          <i class="bi bi-check-circle-fill"></i> Sauvegardé !
        </div>
      </transition>
    </div>

    <!-- Partie droite : bouton supprimer -->
    <div class="d-flex align-items-center ms-3">
      <button
        @click="deleteTask"
        class="btn btn-outline-danger btn-sm btn-delete d-flex align-items-center justify-content-center"
      >
        <i class="bi bi-trash"></i>
      </button>
    </div>
  </li>
</template>

<script>
import taskService from "../services/taskService";

export default {
  props: { task: Object },
  emits: ["task-deleted", "task-updated"],
  data() {
    return {
      isEditingTitle: false,
      isEditingDescription: false,
      editTitle: this.task.title,
      editDescription: this.task.description,
      saveSuccess: false,
    };
  },
  methods: {
    startEditing(field) {
      if (field === "title") this.isEditingTitle = true;
      if (field === "description") this.isEditingDescription = true;
    },

    async saveTask() {
      const updatedTask = {
        title: this.editTitle,
        description: this.editDescription.trim() || "",
        isCompleted: this.task.isCompleted,
      };
      try {
        const response = await taskService.updateTask(this.task.id, updatedTask);
        this.isEditingTitle = false;
        this.isEditingDescription = false;
        this.$emit("task-updated", response.data);
        this.saveSuccess = true;
        setTimeout(() => (this.saveSuccess = false), 1500);
      } catch (error) {
        console.error("Erreur update :", error);
      }
    },

    async deleteTask() {
      try {
        await taskService.deleteTask(this.task.id);
        this.$emit("task-deleted", this.task.id);
      } catch (error) {
        console.error("Erreur suppression :", error);
      }
    },

    async toggleCompletion() {
      const updatedTask = { ...this.task, isCompleted: !this.task.isCompleted };
      try {
        const response = await taskService.updateTask(this.task.id, updatedTask);
        this.$emit("task-updated", response.data);
      } catch (error) {
        console.error("Erreur statut :", error);
      }
    },
  },
};
</script>

<style scoped>
/* Masquer la checkbox classique */
.custom-checkbox {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border: 2px solid #6c757d;
  border-radius: 50%;
  outline: none;
  cursor: pointer;
  position: relative;
  transition: all 0.2s;
}
/* Quand coché */
.custom-checkbox:checked {
  background-color: #28a745;
  border-color: #28a745;
}
/* Checkmark */
.custom-checkbox:checked::after {
  content: "✔";
  color: white;
  font-size: 14px;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
/* Hover pour li non complétés */
li:not(.completed):hover {
  background-color: #f0f0f0;
  transition: background-color 0.2s;
}
.completed {
  color: #888;
  background-color: #b5cfb3;
  transition: background-color 0.2s;
}
.completed:hover {
  background-color: #b7e3b4;
}
/* Curseur + hover */
.editable {
  cursor: text;
  transition: background-color 0.2s;
  padding-right: 0.5rem;
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
/* Bouton supprimer : masqué par défaut */
li .btn-delete {
  opacity: 0;
  transform: translateX(5px) scale(0.9);
  transition: opacity 0.25s ease, transform 0.25s ease;
  pointer-events: none;
}
/* Affichage au survol du li */
li:hover .btn-delete {
  opacity: 1;
  transform: translateX(0) scale(1);
  pointer-events: auto;
}

/* Feedback */
.text-success {
  transition: opacity 0.3s ease;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.task-item {
  transition: all 0.5s ease;
}
</style>
