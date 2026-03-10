"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";

export default function TeacherClassesPage() {
  const { data, isLoading } = useQuery({
    queryKey: ["teacher-classes"],
    queryFn: () => api.get("/teacher/classes").then((res) => res.data),
  });

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">My Classes</h1>
      <div className="grid gap-4">
        {data?.classes?.map((cls: any) => (
          <div key={cls.id} className="p-4 border rounded">
            <h2>{cls.classRoom?.name} - {cls.subject?.name}</h2>
          </div>
        ))}
      </div>
    </div>
  );
}