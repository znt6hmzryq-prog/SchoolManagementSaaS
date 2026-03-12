"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  HomeIcon,
  UsersIcon,
  BookOpenIcon,
  CalendarDaysIcon,
  ClipboardDocumentListIcon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
} from "@heroicons/react/24/outline";
import React from "react";

const items = [
  { href: "/dashboard", label: "Dashboard", icon: HomeIcon },
  { href: "/dashboard/students", label: "Students", icon: UsersIcon },
  { href: "/dashboard/teachers", label: "Teachers", icon: BookOpenIcon },
  { href: "/dashboard/classes", label: "Classes", icon: ClipboardDocumentListIcon },
  { href: "/dashboard/attendance", label: "Attendance", icon: CalendarDaysIcon },
  { href: "/dashboard/grades", label: "Grades", icon: ClipboardDocumentListIcon },
  { href: "/dashboard/settings", label: "Settings", icon: Cog6ToothIcon },
];

export default function Sidebar() {
  const path = usePathname();

  return (
    <aside className="w-60 bg-white border-r hidden md:flex flex-col">
      <div className="p-6 border-b">
        <Link href="/dashboard" className="text-lg font-semibold">
          School SaaS
        </Link>
      </div>

      <nav className="flex-1 p-4 space-y-1">
        {items.map((item) => {
          const active = path === item.href;
          const Icon = item.icon as any;
          return (
            <Link
              key={item.href}
              href={item.href}
              className={`flex items-center gap-3 p-2 rounded-md hover:bg-gray-100 ${
                active ? "bg-gray-100 font-medium" : ""
              }`}
            >
              <Icon className="h-5 w-5 text-gray-600" />
              <span>{item.label}</span>
            </Link>
          );
        })}
      </nav>

      <div className="p-4 border-t">
        <button
          onClick={() => {
            localStorage.removeItem("token");
            window.location.href = "/login";
          }}
          className="w-full flex items-center gap-2 p-2 rounded-md text-left hover:bg-gray-100"
        >
          <ArrowRightOnRectangleIcon className="h-5 w-5 text-gray-600" /> Logout
        </button>
      </div>
    </aside>
  );
}
