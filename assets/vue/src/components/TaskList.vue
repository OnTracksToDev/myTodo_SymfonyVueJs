<template>
    <div class="row justify-content-center mt-4">
      <div class="col-12 col-sm-6">
        <h2>Liste des tâches</h2>

        <!-- Formulaire pour ajouter une tâche -->
        <TaskForm @task-added="addTaskToList" />

        <!-- Contrôles : filtre, tri, barre de progression -->
        <TaskControls
          :tasks="sortedFilteredTasks"
          @filter-changed="setFilter"
          @sort-changed="setSort"
        />

        <!-- Liste des tâches -->
        <transition-group name="task" tag="ul" class="list-group">
          <TaskItem
            v-for="task in sortedFilteredTasks"
            :key="task.id"
            :task="task"
            @task-deleted="removeTaskFromList"
            @task-updated="updateTaskInList"
          />
        </transition-group>
        <!-- Message si aucune tâche -->
        <div
          v-if="sortedFilteredTasks.length === 0"
          class="text-center text-muted mt-3"
        >
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
    },
    removeTaskFromList(taskId) {
      this.tasks = this.tasks.filter((t) => t.id !== taskId);
    },
    updateTaskInList(updatedTask) {
      const index = this.tasks.findIndex((t) => t.id === updatedTask.id);
      if (index !== -1) this.tasks.splice(index, 1, updatedTask);
    },
  },
};
</script>

<style scoped>
.list-group {
  padding-left: 0;
  margin-top: 10px;
}
h2 {
  margin-bottom: 10px;
  color: #2c3e50;
}
/* Transitions */
.task-enter-active,
.task-leave-active {
  transition: all 0.4s ease;
}
.task-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}
.task-enter-to {
  opacity: 1;
  transform: translateY(0);
}
.task-leave-from {
  opacity: 1;
  transform: translateY(0);
}
.task-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
