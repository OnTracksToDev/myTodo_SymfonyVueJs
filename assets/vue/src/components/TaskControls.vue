<template>
  <div class="task-controls mb-3">
    <div class="task-controls mb-3">
      <!-- Barre de progression -->
      <div class="row mb-2 p-4">
        <div class="col-12">
          <p class="mb-1 text-center text-dark">
            <span v-if="tasks.length">
              {{ doneCount }}/{{ tasks.length }} tâche{{
                doneCount > 1 ? "s" : ""
              }}
              terminée{{ doneCount > 1 ? "s" : "" }}
            </span>
            <span v-else> Aucune tâche pour le moment </span>
          </p>
          <div class="progress custom-progress" style="height: 20px">
            <div
              class="progress-bar"
              role="progressbar"
              :style="{ width: tasks.length ? progress + '%' : '0%' }"
              :aria-valuenow="doneCount"
              aria-valuemin="0"
              :aria-valuemax="tasks.length || 1"
            ></div>
          </div>
        </div>
      </div>

      <!-- Filtre -->
      <div class="row mb-2">
        <div class="col-12 d-grid">
          <div class="btn-group" role="group">
            <button
              class="btn btn-outline-secondary"
              :class="{ active: currentFilter === 'all' }"
              @click="setFilter('all')"
            >
              Toutes
            </button>
            <button
              class="btn btn-outline-secondary"
              :class="{ active: currentFilter === 'active' }"
              @click="setFilter('active')"
            >
              Actives
            </button>
            <button
              class="btn btn-outline-secondary"
              :class="{ active: currentFilter === 'done' }"
              @click="setFilter('done')"
            >
              Terminées
            </button>
          </div>
        </div>
      </div>

      <!-- Tri -->
      <div class="row mb-2">
        <div class="col-12 col-md-2 d-flex align-items-center justify-content-md-center">
          <label class="form-label  mb-0 text-dark">Tri :</label>
        </div>
        <div class="col-12 col-md-10 d-flex align-items-center">
          <select
            class="form-select flex-grow-1"
            v-model="sortValue"
            @change="emitSort"
          >
            <option value="date_asc">Date ↑</option>
            <option value="date_desc">Date ↓</option>
            <option value="status">Statut</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "TaskControls",
  props: {
    tasks: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      sortValue: "date_desc",
      currentFilter: "all",
    };
  },
  computed: {
    doneCount() {
      return this.tasks.filter((t) => t.isCompleted).length;
    },
    progress() {
      return this.tasks.length ? (this.doneCount / this.tasks.length) * 100 : 0;
    },
  },
  methods: {
    setFilter(filter) {
      this.currentFilter = filter;
      this.$emit("filter-changed", filter);
    },
    emitSort() {
      this.$emit("sort-changed", this.sortValue);
    },
  },
};
</script>
<style scoped>
.task-controls > * {
  margin-bottom: 1rem;
}

.custom-progress {
  height: 20px;
  background-color: #e9ecef; /* gris clair de fond */
  border-radius: 10px;
  overflow: hidden;
}

.custom-progress .progress-bar {
  background: linear-gradient(90deg, #a4d5b8, #007e33); /* dégradé vert */
  transition: width 0.4s ease;
}
</style>
