"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";

export default function AnalyticsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ["analytics"],
    queryFn: () => api.get("/super-admin/growth-analytics").then((res) => res.data),
  });

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">Analytics</h1>
      <p>Growth Percentage: {data?.growth_percentage}%</p>
    </div>
  );
}