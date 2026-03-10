"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";

export default function StudentAttendancePage() {
  const { data, isLoading } = useQuery({
    queryKey: ["student-attendance"],
    queryFn: () => api.get("/student/attendance").then((res) => res.data),
  });

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">My Attendance</h1>
      <div className="grid gap-4">
        {data?.attendance?.map((att: any) => (
          <div key={att.id} className="p-4 border rounded">
            <p>Status: {att.status}</p>
            <p>Date: {att.date}</p>
          </div>
        ))}
      </div>
    </div>
  );
}