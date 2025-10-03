<template>
  <div class="task-controls mb-3">
    <div class="task-controls mb-3">
      <!-- Barre de progression -->
      <div class="row mb-2 p-4">
        <div class="col-12">
          <p class="mb-1 text-center fw-bold text-dark">
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
        <div class="col-12 d-flex align-items-center">
          <div class="dropdown flex-grow-1">
            <button
              class="btn btn-outline-secondary dropdown-toggle w-100 text-center"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <span v-if="sortValue === 'date_asc'"
                ><i class="bi bi-calendar"></i> Date ↑</span
              >
              <span v-else-if="sortValue === 'date_desc'"
                ><i class="bi bi-calendar"></i> Date ↓</span
              >
              <span v-else-if="sortValue === 'status'"
                ><i class="bi bi-check-circle"></i> Statut</span
              >
            </button>
            <ul class="dropdown-menu w-100">
              <li>
                <a
                  class="dropdown-item d-flex align-items-center"
                  href="#"
                  @click.prevent="setSort('date_asc')"
                >
                  <i class="bi bi-calendar me-2"></i> Date ↑
                </a>
              </li>
              <li>
                <a
                  class="dropdown-item d-flex align-items-center"
                  href="#"
                  @click.prevent="setSort('date_desc')"
                >
                  <i class="bi bi-calendar me-2"></i> Date ↓
                </a>
              </li>
              <li>
                <a
                  class="dropdown-item d-flex align-items-center"
                  href="#"
                  @click.prevent="setSort('status')"
                >
                  <i class="bi bi-check-circle me-2"></i> Statut
                </a>
              </li>
            </ul>
          </div>
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
    setSort(value) {
      this.sortValue = value;
      this.emitSort(); // appel à la fonction que tu avais déjà
    },
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
  background-color: #f1f3f5;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.custom-progress .progress-bar {
  background: linear-gradient(90deg, #ffd966, #ffb347);
  transition: width 0.4s ease, background 0.4s ease;
}

.custom-progress:focus,
.custom-progress:hover {
  box-shadow: 0 0 0 0.2rem rgba(255, 176, 71, 0.25);
  outline: none;
  transition: box-shadow 0.2s ease;
}
</style>
