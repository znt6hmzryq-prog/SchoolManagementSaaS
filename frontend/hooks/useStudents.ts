import { useQuery } from "@tanstack/react-query";
import axios from "axios";

const API_URL = "http://127.0.0.1:8000/api/students";

const getStudents = async () => {
  const token = localStorage.getItem("token");

  const res = await axios.get(API_URL, {
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    },
  });

  return res.data;
};

export const useStudents = () => {
  return useQuery({
    queryKey: ["students"],
    queryFn: getStudents,

    // auto refetch when returning to page
    refetchOnWindowFocus: true,

    // small cache time to keep UI fresh
    staleTime: 1000 * 10,

    // retry if request fails
    retry: 1,
  });
};