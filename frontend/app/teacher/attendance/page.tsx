"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";

export default function TeacherAttendancePage() {
  const { data, isLoading } = useQuery({
    queryKey: ["teacher-attendance"],
    queryFn: () => api.get("/teacher/attendance").then((res) => res.data),
  });

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">Attendance</h1>
      <div className="grid gap-4">
        {data?.attendance?.map((att: any) => (
          <div key={att.id} className="p-4 border rounded">
            <p>Student: {att.student?.first_name} {att.student?.last_name}</p>
            <p>Status: {att.status}</p>
            <p>Date: {att.date}</p>
          </div>
        ))}
      </div>
    </div>
  );
}