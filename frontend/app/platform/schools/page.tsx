"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";

export default function SchoolsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ["schools"],
    queryFn: () => api.get("/super-admin/schools").then((res) => res.data),
  });

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">Schools</h1>
      <div className="grid gap-4">
        {data.map((school: any) => (
          <div key={school.id} className="p-4 border rounded">
            <h2>{school.name}</h2>
            <p>Students: {school.students_count}</p>
            <p>Teachers: {school.teachers_count}</p>
          </div>
        ))}
      </div>
    </div>
  );
}