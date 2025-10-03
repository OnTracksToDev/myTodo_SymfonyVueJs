<template>
  <div class="row justify-content-center mt-4">
    <div class="col-12 col-sm-6">
      <h2 class="app-title text-center">
        <i class="bi bi-list-task me-2"></i> Liste des tâches
      </h2>
      <!-- Formulaire pour ajouter une tâche -->
      <TaskForm @task-added="addTaskToList" />

      <!-- Contrôles : filtre, tri, barre de progression -->
      <TaskControls
        :tasks="sortedFilteredTasks"
        @filter-changed="setFilter"
        @sort-changed="setSort"
      />

      <!-- Liste des tâches -->
      <transition-group
        name="task-transition"
        tag="div"
        class="task-list-container"
      >
        <div
          v-for="task in sortedFilteredTasks"
          :key="task.id"
          class="list-group task-transition-item"
        >
          <TaskItem
            :task="task"
            @task-deleted="removeTaskFromList"
            @task-updated="updateTaskInList"
          />
        </div>
      </transition-group>

      <!-- Toast global -->
      <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div
          v-if="toastMessage"
          class="toast align-items-center text-bg-warning border-0 show"
          role="alert"
        >
          <div class="d-flex">
            <div class="toast-body">{{ toastMessage }}</div>
          </div>
        </div>
      </div>

      <!-- Message si aucune tâche -->
      <div
        v-if="sortedFilteredTasks.length === 0"
        class="text-center text-muted py-5"
      >
        <i class="bi bi-inbox display-4 d-block mb-3"></i>
        <span v-if="filter === 'all'">Aucune tâche disponible.</span>
        <span v-else-if="filter === 'active'">Aucune tâche active.</span>
        <span v-else-if="filter === 'done'">Aucune tâche terminée.</span>
      </div>
    </div>
  </div>
</template>

<script>
import TaskItem from "./TaskItem.vue";
import TaskForm from "./TaskForm.vue";
import TaskControls from "./TaskControls.vue";
import taskService from "../services/taskService";

export default {
  name: "TaskList",
  components: { TaskItem, TaskForm, TaskControls },
  data() {
    return {
      tasks: [],
      filter: "all",
      sort: "date_desc",
      toastMessage: "",
    };
  },
  created() {
    this.fetchTasks();
  },
  computed: {
    // Liste filtrée et triée
    sortedFilteredTasks() {
      let filtered = [...this.tasks];

      // Filtre
      if (this.filter === "active")
        filtered = filtered.filter((t) => !t.isCompleted);
      if (this.filter === "done")
        filtered = filtered.filter((t) => t.isCompleted);

      // Tri
      if (this.sort === "date_asc") {
        filtered.sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt));
      } else if (this.sort === "date_desc") {
        filtered.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
      } else if (this.sort === "status") {
        filtered.sort((a, b) => a.isCompleted - b.isCompleted);
      }

      return filtered;
    },
  },
  methods: {
    async fetchTasks() {
      try {
        const response = await taskService.getTasks();
        this.tasks = response.data;
      } catch (error) {
        console.error("Erreur lors de la récupération des tâches :", error);
      }
    },
    setFilter(value) {
      this.filter = value;
    },
    setSort(value) {
      this.sort = value;
    },
    addTaskToList(task) {
      // Nouvelle tâche en haut
      this.tasks.unshift(task);
      this.showToast(`✅ Tâche "${task.title}" ajoutée !`);
    },
    removeTaskFromList(taskId) {
      const index = this.tasks.findIndex((t) => t.id === taskId);
      if (index !== -1) {
        const title = this.tasks[index].title;
        // Attendre la fin de la transition avant de supprimer
        setTimeout(() => {
          this.tasks.splice(index, 1);
          this.showToast(`🗑️ Tâche "${title}" supprimée`);
        }, 400);
      }
    },
    updateTaskInList(updatedTask) {
      const index = this.tasks.findIndex((t) => t.id === updatedTask.id);
      if (index !== -1) this.tasks.splice(index, 1, updatedTask);
    },
    showToast(msg) {
      this.toastMessage = msg;
      setTimeout(() => (this.toastMessage = ""), 2000);
    },
  },
};
</script>

<style scoped>
.task-list-container {
  padding-left: 0;
  margin-top: 15px;
}

/* Transitions pour l'entrée et la sortie */
.task-transition-enter-active,
.task-transition-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.task-transition-enter-from {
  opacity: 0;
  transform: translateX(30px) scale(0.95);
}

.task-transition-enter-to {
  opacity: 1;
  transform: translateX(0) scale(1);
}

.task-transition-leave-from {
  opacity: 1;
  transform: translateX(0) scale(1);
}

.task-transition-leave-to {
  opacity: 0;
  transform: translateX(-30px) scale(0.95);
}

/* Transition pour le réarrangement des éléments */
.task-transition-move {
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Style pour chaque item */
.task-transition-item {
  margin-bottom: 5px;
}
</style>
