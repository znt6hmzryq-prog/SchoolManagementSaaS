"use client";

import { useAuth } from "@/hooks/useAuth";
import Sidebar from "@/components/Sidebar";
import Navbar from "@/components/Navbar";
import AIAssistant from "@/components/AIAssistant";

export default function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const { isAuthenticated } = useAuth();

  if (!isAuthenticated) {
    return <div>Loading...</div>; // Or redirect handled in useAuth
  }

  return (
    <div className="flex h-screen bg-gray-100">
      <Sidebar />
      <div className="flex-1 flex flex-col min-w-0">
        <Navbar />
        <main className="flex-1 p-4 md:p-6 overflow-auto">{children}</main>
      </div>
      <AIAssistant />
    </div>
  );
}