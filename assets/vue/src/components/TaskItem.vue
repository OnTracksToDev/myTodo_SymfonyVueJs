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
        const response = await taskService.updateTask(
          this.task.id,
          updatedTask
        );
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
/* Masquer la checkbox classique */
.custom-checkbox {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  width: 22px;
  height: 22px;
  border: 2px solid #d0d5dd;
  border-radius: 50%;
  cursor: pointer;
  position: relative;
  background: #fff;
  transition: all 0.25s ease;
}
.custom-checkbox:hover {
  border-color: #7598d4;
}

/* Quand coché */
.custom-checkbox:checked {
  background-color: #b7e4c7;
  border-color: #28a745;
}
.custom-checkbox:checked::after {
  content: "✔";
  color: white;
  font-size: 14px;
  font-weight: bold;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

/* Hover pour li non complétés */

li:not(.completed):hover {
  background: #f9fbff;
  border-color: #d0e2ff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.completed {
  background: #f1f8f5;
  border-color: #b7e4c7;
  color: #6b7280;
  text-decoration: line-through;
  opacity: 0.9;
}
.completed:hover {
  background: #e0f2e9;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* Curseur + hover */
.editable {
  cursor: text;
  padding: 4px 6px;
  border-radius: 8px;
  transition: background-color 0.25s ease, box-shadow 0.25s ease;
}

.editable:hover {
  background-color: #e0f2ff;
  box-shadow: 0 2px 6px rgba(0, 123, 255, 0.15);
}

/* Icône crayon */
.edit-icon {
  font-size: 0.85rem;
  color: #007bff;
  opacity: 0;
  margin-left: 6px;
  transition: opacity 0.25s ease;
}
.editable:hover .edit-icon {
  opacity: 1;
}
.completed .editable:hover {
  background-color: #bad2c1;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}
/* Bouton supprimer : masqué par défaut */
li .btn-delete {
  opacity: 0;
  transform: translateX(5px) scale(0.9);
  transition: opacity 0.25s ease, transform 0.25s ease, color 0.25s ease;
  color: #ef4444;
  border: none;
  background: transparent;
  font-size: 1rem;
}
/* Affichage au survol du li */
li:hover .btn-delete {
  opacity: 1;
  transform: translateX(0) scale(1);
  pointer-events: auto;
}
li .btn-delete:hover {
  color: #dc2626;
  transform: scale(1.25);
  background: transparent;
}
/* Focus / active */
li .btn-delete:focus,
li .btn-delete:active {
  background: transparent;
}

/* Feedback */
.text-success {
  transition: opacity 0.3s ease;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
