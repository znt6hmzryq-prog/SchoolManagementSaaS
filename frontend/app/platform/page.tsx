"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";
import DashboardCard from "@/components/DashboardCard";

export default function PlatformPage() {
  const { data, isLoading } = useQuery({
    queryKey: ["platform-overview"],
    queryFn: async () => {
      const [schools, subscriptions, revenue, growth] = await Promise.all([
        api.get("/super-admin/total-schools"),
        api.get("/super-admin/active-subscriptions"),
        api.get("/super-admin/monthly-revenue"),
        api.get("/super-admin/growth-analytics"),
      ]);
      return {
        totalSchools: schools.data.total_schools,
        activeSubscriptions: subscriptions.data.active_subscriptions,
        monthlyRevenue: revenue.data.monthly_revenue,
        growth: growth.data.growth_percentage,
      };
    },
  });

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">Platform Dashboard</h1>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <DashboardCard title="Total Schools" value={data?.totalSchools} />
        <DashboardCard title="Active Subscriptions" value={data?.activeSubscriptions} />
        <DashboardCard title="Monthly Revenue" value={`$${data?.monthlyRevenue}`} />
        <DashboardCard title="Growth %" value={`${data?.growth}%`} />
      </div>
    </div>
  );
}