<template>
  <div class="task-controls">
    <!-- Filtre -->
    <div class="filter-buttons">
      <button @click="$emit('filter-changed', 'all')">Toutes</button>
      <button @click="$emit('filter-changed', 'active')">Actives</button>
      <button @click="$emit('filter-changed', 'done')">Terminées</button>
    </div>

    <!-- Tri -->
    <div class="sort-select">
      <label>Tri : </label>
      <select v-model="sortValue" @change="emitSort">
        <option value="date_asc">Date ↑</option>
        <option value="date_desc">Date ↓</option>
        <option value="status">Statut</option>
      </select>
    </div>

    <!-- Barre de progression -->
    <div v-if="tasks.length" class="progress-bar">
      <p>{{ doneCount }}/{{ tasks.length }} tâches terminées</p>
      <div class="bar-background">
        <div class="bar-foreground" :style="{ width: progress + '%' }"></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TaskControls',
  props: {
    tasks: {
      type: Array,
      required: true
    }
  },
  data() {
    return { sortValue: 'date_asc' };
  },
  computed: {
    doneCount() {
      return this.tasks.filter(t => t.isCompleted).length;
    },
    progress() {
      return this.tasks.length ? (this.doneCount / this.tasks.length) * 100 : 0;
    }
  },
  methods: {
    emitSort() {
      this.$emit('sort-changed', this.sortValue);
    }
  }
};
</script>

<style scoped>
.filter-buttons button { margin-right: 5px; }
.bar-background {
  background: #eee;
  width: 200px;
  height: 10px;
  border-radius: 5px;
  margin-top: 5px;
}
.bar-foreground {
  background: #4caf50;
  height: 10px;
  border-radius: 5px;
}
</style>
