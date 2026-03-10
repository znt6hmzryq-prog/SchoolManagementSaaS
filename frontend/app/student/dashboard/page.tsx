"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";

export default function StudentDashboardPage() {
  const { data, isLoading } = useQuery({
    queryKey: ["student-dashboard"],
    queryFn: () => api.get("/student/dashboard").then((res) => res.data),
  });

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">Student Dashboard</h1>
      <p>Student: {data?.student}</p>
      <p>Class: {data?.class}</p>
      <p>CGPA: {data?.cgpa}</p>
      <p>Attendance Rate: {data?.attendance_rate_percent}%</p>
    </div>
  );
}