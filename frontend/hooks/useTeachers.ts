"use client";

import { useQuery } from "@tanstack/react-query";
import { getTeachers } from "@/services/api";

export const useTeachers = () => {
  return useQuery({
    queryKey: ["teachers"],
    queryFn: getTeachers,
  });
};