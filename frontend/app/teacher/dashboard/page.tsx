"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";

export default function TeacherDashboardPage() {
  const { data, isLoading } = useQuery({
    queryKey: ["teacher-dashboard"],
    queryFn: () => api.get("/teacher/dashboard").then((res) => res.data),
  });

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">Teacher Dashboard</h1>
      <div>
        <h2>Classes</h2>
        {data?.classes?.map((cls: any) => (
          <div key={cls.subject} className="border p-4 mb-4">
            <h3>{cls.subject} - {cls.class}</h3>
            <p>Total Students: {cls.total_students}</p>
            <p>Class Average CGPA: {cls.class_average_cgpa}</p>
          </div>
        ))}
      </div>
    </div>
  );
}