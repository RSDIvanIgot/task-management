<template>
  <div class="task-card" :data-id="task.id" @click="handleEditClick">
    <p>{{ task.title }}</p>
  </div>
</template>

<script>
export default {
  props: {
    task: Object,
  },
  methods: {
    handleEditClick() {
      this.$emit('edit-task', this.task);
    },
    editTask(task) {
      this.$emit('edit-task', task);
    },
    deleteTask(task) {
      this.$emit('delete-task', task);
    },
  },
};
</script>

<style scoped>
.task-card {
  background-color: #ffffff;
  border-radius: 4px;
  box-shadow: 0 1px 0 rgba(9, 30, 66, 0.25);
  cursor: pointer;
  padding: 8px 10px;
  margin-bottom: 8px;
  transition: background-color 0.2s, opacity 0.2s;
}

.task-card:hover {
  background-color: #f4f5f7;
}
</style>
