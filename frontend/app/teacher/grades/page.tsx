"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";

export default function TeacherGradesPage() {
  const { data, isLoading } = useQuery({
    queryKey: ["teacher-grades"],
    queryFn: () => api.get("/teacher/grades").then((res) => res.data),
  });

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">Grades</h1>
      <div className="grid gap-4">
        {data?.grades?.map((grade: any) => (
          <div key={grade.id} className="p-4 border rounded">
            <p>Student: {grade.student?.first_name} {grade.student?.last_name}</p>
            <p>Assessment: {grade.assessment?.title}</p>
            <p>Grade: {grade.grade}</p>
          </div>
        ))}
      </div>
    </div>
  );
}