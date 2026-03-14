import axios from "axios";

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api",
  headers: {
    "Content-Type": "application/json",
  },
});

// Add request interceptor to attach token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

export default api;

// Convenience service helpers using the shared axios instance
export const getStudents = async () => {
  const res = await api.get('/students');
  return res.data;
};

export const createStudent = async (payload: any) => {
  const res = await api.post('/students', payload);
  return res.data;
};

export const getTeachers = async () => {
  const res = await api.get('/teachers');
  return res.data;
};

export const createTeacher = async (payload: any) => {
  const res = await api.post('/teachers', payload);
  return res.data;
};

export const getClasses = async () => {
  const res = await api.get('/class-rooms');
  return res.data;
};

export const createClass = async (payload: any) => {
  const res = await api.post('/class-rooms', payload);
  return res.data;
};

export const getAttendance = async () => {
  const res = await api.get('/attendances');
  return res.data;
};

export const markAttendance = async (payload: any) => {
  const res = await api.post('/attendances', payload);
  return res.data;
};

export const getGrades = async () => {
  const res = await api.get('/grades');
  return res.data;
};

export const submitGrade = async (payload: any) => {
  const res = await api.post('/grades', payload);
  return res.data;
};

export const createCheckout = async (payload: any) => {
  const res = await api.post('/billing/checkout', payload);
  return res.data;
};