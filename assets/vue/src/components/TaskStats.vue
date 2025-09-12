<template>
  <div v-if="total > 0" class="task-stats">
    {{ completed }} sur {{ total }} tâche{{ total > 1 ? 's' : '' }} terminée{{ completed > 1 ? 's' : '' }}
  </div>
</template>

<script>
export default {
  props: {
    tasks: {
      type: Array,
      required: true
    }
  },
  computed: {
    total() {
      return this.tasks.length;
    },
    completed() {
      return this.tasks.filter(t => t.isCompleted).length;
    }
  }
};
</script>

<style scoped>
.task-stats {
  font-weight: bold;
  margin-bottom: 10px;
  color: #2c3e50;
}
</style>
