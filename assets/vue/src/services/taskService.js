import axios from 'axios';

const API_URL = 'http://127.0.0.1:8000/api/tasks';

export default {
  getTasks() {
    return axios.get(API_URL);
  },
  createTask(task) {
    return axios.post(API_URL, task);
  },
  updateTask(id, task) {
    return axios.put(`${API_URL}/${id}`, task);
  },
  deleteTask(id) {
    return axios.delete(`${API_URL}/${id}`);
  },
};