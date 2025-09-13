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
    <ul>
      <TaskItem 
        v-for="task in tasks" 
        :key="task.id" 
        :task="task" 
        @task-deleted="removeTaskFromList"
        @task-updated="updateTaskInList"
      />
    </ul>
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
    // Récupère tâches API avec filtre et tri
    async fetchTasks() {
      try {
        const response = await taskService.getTasks({ filter: this.filter, sort: this.sort });
        this.tasks = response.data; 
      } catch (error) {
        console.error("Erreur lors de la récupération des tâches :", error);
      }
    },

    // Changement filtre
    setFilter(value) {
      this.filter = value;
      this.fetchTasks();
    },

    // Changement tri
    setSort(value) {
      this.sort = value;
      this.fetchTasks();
    },

    // Ajout tâche
    addTaskToList(task) {
      this.tasks.push(task);
    },

    // Suppression tâche
    removeTaskFromList(taskId) {
      this.tasks = this.tasks.filter(t => t.id !== taskId);
    },

    // Mise à jour tâche
    updateTaskInList(updatedTask) {
      const index = this.tasks.findIndex(t => t.id === updatedTask.id);
      if (index !== -1) this.tasks.splice(index, 1, updatedTask);
    }
  }
};
</script>

<style scoped>
ul {
  padding-left: 0;
  list-style: none;
}
h2 {
  margin-bottom: 10px;
  color: #2c3e50;
}
</style>
