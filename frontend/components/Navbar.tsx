"use client";

import { useRouter } from "next/navigation";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import api from "@/services/api";
import { useState } from "react";
import { NotificationDropdown } from "./NotificationDropdown";

const Navbar = () => {
  const router = useRouter();
  const queryClient = useQueryClient();
  const [showNotifications, setShowNotifications] = useState(false);

  const { data: notifications } = useQuery({
    queryKey: ["notifications"],
    queryFn: () => api.get("/notifications").then((res) => res.data),
  });

  const markAsReadMutation = useMutation({
    mutationFn: (id: number) => api.patch(`/notifications/${id}/read`),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ["notifications"] }),
  });

  const handleLogout = () => {
    localStorage.removeItem("token");
    router.push("/login");
  };

  const handleMarkAsRead = (id: number) => {
    markAsReadMutation.mutate(id);
  };

  const unreadCount = notifications?.data?.filter((n: any) => !n.read_at).length || 0;

  return (
    <div className="bg-white shadow-md p-4 flex justify-between items-center relative">
      <h1 className="text-lg font-semibold">Dashboard</h1>
      <div className="flex items-center space-x-4">
        <button
          onClick={() => setShowNotifications(!showNotifications)}
          className="relative p-2 hover:bg-gray-100 rounded-full"
        >
          🔔
          {unreadCount > 0 && (
            <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1 min-w-[18px] h-5 flex items-center justify-center">
              {unreadCount > 99 ? '99+' : unreadCount}
            </span>
          )}
        </button>
        <button
          onClick={handleLogout}
          className="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors"
        >
          Logout
        </button>
      </div>

      {showNotifications && (
        <NotificationDropdown
          notifications={notifications?.data || []}
          onMarkAsRead={handleMarkAsRead}
          onClose={() => setShowNotifications(false)}
        />
      )}
    </div>
  );
};

export default Navbar;