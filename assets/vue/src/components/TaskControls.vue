<template>
  <div class="task-controls mb-3">
    <!-- Filtre -->
    <div class="btn-group mb-2" role="group" aria-label="Filtre tâches">
      <button type="button" class="btn btn-outline-primary" @click="$emit('filter-changed', 'all')">Toutes</button>
      <button type="button" class="btn btn-outline-primary" @click="$emit('filter-changed', 'active')">Actives</button>
      <button type="button" class="btn btn-outline-primary" @click="$emit('filter-changed', 'done')">Terminées</button>
    </div>

    <!-- Tri -->
    <div class="mb-2">
      <label class="form-label me-2">Tri :</label>
      <select class="form-select w-auto d-inline-block" v-model="sortValue" @change="emitSort">
        <option value="date_asc">Date ↑</option>
        <option value="date_desc">Date ↓</option>
        <option value="status">Statut</option>
      </select>
    </div>

    <!-- Barre de progression -->
    <div v-if="tasks.length" class="mb-2">
      <p class="mb-1">{{ doneCount }}/{{ tasks.length }} tâches terminées</p>
      <div class="progress">
        <div 
          class="progress-bar" 
          role="progressbar" 
          :style="{ width: progress + '%' }" 
          :aria-valuenow="doneCount" 
          :aria-valuemin="0" 
          :aria-valuemax="tasks.length">
        </div>
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
    return { sortValue: 'date_desc' };
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
.task-controls > * {
  margin-bottom: 1rem;
}
</style>