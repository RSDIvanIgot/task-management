<template>
    <div class="kanban-board">
        <div v-for="column in columns" :key="column.id" class="board-column" :data-id="column.id" :data-status="column.status_name">
            <p class="status-header">{{ column.title }}</p>
            <draggable v-model="tasks[column.status_name]" @end="handleTaskDrop(column.status_name, $event)" group="tasks">
                <task-card
                v-for="(task, index) in tasks[column.status_name]"
                    :key="task.id"
                    :task="task"
                    :index="index"
                    :data-index="index"
                    :data-id="task.id"
                    @edit-task="editTask"
                    @delete-task="deleteTask"
                />
            </draggable>
            <button @click="showTaskModal(column.status_name)" v-if="column.status_name === 'todo'">+ New Task</button>
        </div>

        <div v-if="isTaskModalVisible" class="modal">
            <div class="modal-content">
                <h3 v-if="!selectedTask">Create Task</h3>
                <h3 v-else>Edit Task</h3>
                <input v-model="editedTask.title" placeholder="Title">
                <textarea v-model="editedTask.description" placeholder="Description"></textarea>
                <input type="date" v-model="editedTask.due_date" placeholder="Due Date">
                <button @click="createOrUpdateTask">{{ selectedTask ? 'Save' : 'Create' }}</button>
                <button @click="deleteTask" v-if="selectedTask">Delete</button>
                <button @click="closeTaskModal">Cancel</button>
            </div>
        </div>
    </div>
  </template>

<script>
import draggable from 'vuedraggable';
import TaskCard from './TaskCard.vue';
import axios from 'axios';

export default {
    components: {
        draggable,
        TaskCard,
    },
  data() {
    return {
      tasks: {
        todo: [],
        inProgress: [],
        done: [],
      },
      columns: [],
      isTaskModalVisible: false,
      selectedTask: null,
      editedTask: {
        id: null,
        title: '',
        description: '',
        due_date: new Date().toISOString().substr(0, 10),
        status: 'todo',
      },
    };
  },
  mounted() {
    this.fetchTasks();
    this.fetchColumns();
  },
  methods: {
    async fetchTasks() {
      try {
        const response = await axios.get('/api/tasks');
        this.organizeTasksByColumn(response.data);
      } catch (error) {
        this.$toasted.error(error)
      }
    },
    organizeTasksByColumn(tasks) {
      this.tasks.todo = tasks.filter(task => task.column_id === 1);
      this.tasks.inProgress = tasks.filter(task => task.column_id === 2);
      this.tasks.done = tasks.filter(task => task.column_id === 3);
    },
    async fetchColumns() {
      try {
        const response = await axios.get('/api/columns');
        this.columns = response.data;
      } catch (error) {
        this.$toasted.error(error)
      }
    },
    async createOrUpdateTask() {
      try {
        if (this.selectedTask) {
          await axios.put(`/api/tasks/${this.selectedTask.id}`, this.editedTask);
        } else {
            const currentColumn = this.columns.find(column => column.status_name === this.editedTask.status);

            if (currentColumn) {
                this.editedTask.column_id = currentColumn.id;
            }

            await axios.post('/api/tasks', this.editedTask);
        }
        this.fetchTasks();
        this.closeTaskModal();
      } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
            const errorMessages = Object.values(error.response.data.errors).flat();
            errorMessages.forEach(errorMessage => {
                this.$toasted.error(errorMessage);
            });
        } else {
            this.$toasted.error('An error occurred while processing your request.');
        }
      }
    },
    async moveTask(task, newStatus) {
      try {
        await axios.put(`/api/tasks/${task.id}`, { status: newStatus });
        this.fetchTasks();
      } catch (error) {
        this.$toasted.error(error)
      }
    },
    editTask(task) {
      this.selectedTask = task;
      this.editedTask = { ...task };
      this.showTaskModal();
    },
    deleteTask(task) {
      if (confirm('Are you sure you want to delete this task?')) {
        this.performDeleteTask(this.task);
      }
    },
    async performDeleteTask() {
        try {
            if (!this.selectedTask) {
                return;
            }
            const taskId = this.selectedTask.id;

            await axios.delete(`/api/tasks/${taskId}`);

            for (const columnName in this.tasks) {
                this.tasks[columnName] = this.tasks[columnName].filter(task => task.id !== taskId);
            }

            this.closeTaskModal();
        } catch (error) {
            this.$toasted.error(error)
        }
    },
    showTaskModal(status = 'todo') {
      this.isTaskModalVisible = true;
      this.editedTask.status = status;
    },
    closeTaskModal() {
      this.isTaskModalVisible = false;
      this.selectedTask = null;
      this.editedTask = {
        id: null,
        title: '',
        description: '',
        due_date: new Date().toISOString().substr(0, 10),
        status: 'todo',
      };
    },
    async handleTaskDrop(newStatus, event) {
        try {
            const sourceStatus = event.from.parentElement.getAttribute('data-status');
            const targetStatus = event.to.parentElement.getAttribute('data-status');

            const taskId = event.item.getAttribute('data-id');
            const currentOrder = event.item.getAttribute('data-index');

            if (sourceStatus !== targetStatus) {
                const targetColumn = this.columns.find(column => column.status_name === targetStatus);

                if (targetColumn) {
                    await axios.put(`/api/tasks/${taskId}`, {
                        status: targetStatus,
                        order: currentOrder,
                        column_id: targetColumn.id,
                    });
                }
            }

            for (const column of this.columns) {
                const columnTasks = this.tasks[column.status_name];
                const updatedTasks = columnTasks.map((task, index) => ({
                    id: task.id,
                    order: index,
                    column: column.id,
                }));
                await axios.put('/api/tasks/reorder', { tasks: updatedTasks });
            }
        } catch (error) {
            this.$toasted.error(error)
        }
    },
    handleRequestError(error) {
      if (error.response) {
        console.error('Response Error:', error.response.data);

        this.$refs.toastr.error(error.response.data.message || 'An error occurred.');
      } else if (error.request) {
        console.error('Request Error:', error.request);

        this.$refs.toastr.error('No response received from the server.');
      } else {
        this.$toasted.error(error)

        this.$refs.toastr.error('An error occurred.');
      }
    },
  },
};
</script>

<style scoped>
.status-header {
   margin-bottom: 15px;
   font-weight: bold;
}
.kanban-board {
  display: flex;
  justify-content: space-between;
  padding: 20px;
}

.board-column {
  flex: 1;
  margin: 0 10px;
  background-color: #ebecf0;
  border-radius: 4px;
  padding: 10px;
  min-width: 300px;
  max-height: 90%;
  overflow-y: auto;
}

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

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.modal-content {
  background-color: #fff;
  border-radius: 4px;
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
  padding: 16px;
  max-width: 400px;
  width: 100%;
  position: relative;
}

h3 {
  font-size: 20px;
  margin-bottom: 12px;
}

input,
textarea {
  width: 100%;
  padding: 8px;
  margin-bottom: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
}


button {
  padding: 8px 16px;
  background-color: #0079bf;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-bottom: 10px;
}

button.cancel-button {
  background-color: #aaa;
}

button.delete-button {
  background-color: #e53935;
}

</style>
