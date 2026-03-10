import api from "./api";

export const getTeachers = async () => {
  const response = await api.get("/teachers");
  return response.data;
};

export const createTeacher = async (data: any) => {
  const response = await api.post("/teachers", data);
  return response.data;
};

export const updateTeacher = async (id: number, data: any) => {
  const response = await api.put(`/teachers/${id}`, data);
  return response.data;
};

export const deleteTeacher = async (id: number) => {
  const response = await api.delete(`/teachers/${id}`);
  return response.data;
};