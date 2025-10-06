import axios from 'axios';

const API_URL = '/api/tasks';

export default {
  getTasks(params = {}) {
    return axios.get(API_URL, { params });
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