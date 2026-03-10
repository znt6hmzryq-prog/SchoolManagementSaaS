"use client";

import { useQuery } from "@tanstack/react-query";
import { useEffect, useState } from "react";
import api from "@/services/api";
import DashboardCard from "@/components/DashboardCard";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, BarChart, Bar } from 'recharts';

export default function DashboardPage() {
  const [isDemo, setIsDemo] = useState(false);

  useEffect(() => {
    const demoMode = localStorage.getItem("demoMode");
    setIsDemo(demoMode === "true");
  }, []);

  const { data, isLoading } = useQuery({
    queryKey: ["dashboard"],
    queryFn: () => api.get("/admin/analytics/overview").then((res) => res.data),
  });

  // Mock data for charts - in real app, fetch from API
  const studentGrowthData = [
    { month: 'Jan', students: 100 },
    { month: 'Feb', students: 120 },
    { month: 'Mar', students: 140 },
    { month: 'Apr', students: 160 },
    { month: 'May', students: 180 },
  ];

  const revenueData = [
    { month: 'Jan', revenue: 2000 },
    { month: 'Feb', revenue: 2500 },
    { month: 'Mar', revenue: 3000 },
    { month: 'Apr', revenue: 2800 },
    { month: 'May', revenue: 3500 },
  ];

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      {isDemo && (
        <div className="bg-blue-600 text-white px-4 py-3 mb-6 rounded-lg">
          <div className="flex items-center justify-between">
            <div>
              <strong>Demo Mode:</strong> You're viewing a demo of School Management SaaS. Some features are limited.
            </div>
            <button
              onClick={() => {
                localStorage.removeItem("demoMode");
                window.location.reload();
              }}
              className="bg-blue-700 hover:bg-blue-800 px-3 py-1 rounded text-sm"
            >
              Exit Demo
            </button>
          </div>
        </div>
      )}

      <h1 className="text-3xl font-bold mb-6">Dashboard</h1>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <DashboardCard title="Total Students" value={data?.total_students} />
        <DashboardCard title="Total Teachers" value={data?.total_teachers} />
        <DashboardCard title="Total Classes" value={data?.total_classes} />
        <DashboardCard title="Monthly Revenue" value={`$${data?.monthly_revenue}`} />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white p-6 rounded-lg shadow-md">
          <h2 className="text-xl font-semibold mb-4">Student Growth</h2>
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={studentGrowthData}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip />
              <Line type="monotone" dataKey="students" stroke="#8884d8" strokeWidth={2} />
            </LineChart>
          </ResponsiveContainer>
        </div>

        <div className="bg-white p-6 rounded-lg shadow-md">
          <h2 className="text-xl font-semibold mb-4">Revenue Chart</h2>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={revenueData}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip />
              <Bar dataKey="revenue" fill="#82ca9d" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>
    </div>
  );
}