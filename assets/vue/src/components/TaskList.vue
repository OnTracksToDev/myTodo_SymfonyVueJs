<template>
  <div>
    <h2>Liste des tâches</h2>

    <!-- Formulaire pour ajouter une tâche -->
    <TaskForm @task-added="addTaskToList" />

    <!-- Contrôles : filtre, tri, barre de progression -->
    <TaskControls 
      :tasks="tasks" 
      @filter-changed="setFilter"
      @sort-changed="setSort"
    />

    <!-- Liste des tâches -->
    <transition-group name="task" tag="ul" class="list-group">
      <TaskItem 
        v-for="task in tasks" 
        :key="task.id" 
        :task="task" 
        @task-deleted="removeTaskFromList"
        @task-updated="updateTaskInList"
      />
    </transition-group>
  </div>
</template>

<script>
import TaskItem from "./TaskItem.vue";
import TaskForm from "./TaskForm.vue";
import TaskControls from "./TaskControls.vue";
import taskService from "../services/taskService";

export default {
  name: 'TaskList',
  components: { TaskItem, TaskForm, TaskControls },
  data() {
    return { 
      tasks: [],
      filter: 'all',
      sort: 'date_asc'
    };
  },
  created() {
    this.fetchTasks();
  },
  methods: {
    async fetchTasks() {
      try {
        const response = await taskService.getTasks({ filter: this.filter, sort: this.sort });
        this.tasks = response.data; 
      } catch (error) {
        console.error("Erreur lors de la récupération des tâches :", error);
      }
    },
    setFilter(value) {
      this.filter = value;
      this.fetchTasks();
    },
    setSort(value) {
      this.sort = value;
      this.fetchTasks();
    },
    addTaskToList(task) {
      this.tasks.push(task);
    },
    removeTaskFromList(taskId) {
      this.tasks = this.tasks.filter(t => t.id !== taskId);
    },
    updateTaskInList(updatedTask) {
      const index = this.tasks.findIndex(t => t.id === updatedTask.id);
      if (index !== -1) this.tasks.splice(index, 1, updatedTask);
    }
  }
};
</script>

<style scoped>
/* Liste */
.list-group {
  padding-left: 0;
  margin-top: 10px;
}

/* Titre */
h2 {
  margin-bottom: 10px;
  color: #2c3e50;
}

/* Transitions */
.task-enter-active, .task-leave-active {
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
