"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";

export default function RevenuePage() {
  const { data, isLoading } = useQuery({
    queryKey: ["revenue"],
    queryFn: () => api.get("/super-admin/monthly-revenue").then((res) => res.data),
  });

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">Revenue</h1>
      <p>Monthly Revenue: ${data?.monthly_revenue}</p>
    </div>
  );
}