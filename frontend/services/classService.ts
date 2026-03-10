import api from "./api";

export const getClasses = async () => {
  const response = await api.get("/class-rooms");
  return response.data;
};

export const createClass = async (data: any) => {
  const response = await api.post("/class-rooms", data);
  return response.data;
};

export const updateClass = async (id: number, data: any) => {
  const response = await api.put(`/class-rooms/${id}`, data);
  return response.data;
};

export const deleteClass = async (id: number) => {
  const response = await api.delete(`/class-rooms/${id}`);
  return response.data;
};