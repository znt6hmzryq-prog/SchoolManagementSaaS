"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useState, useEffect } from "react";

const Sidebar = () => {
  const pathname = usePathname();
  const [isCollapsed, setIsCollapsed] = useState(false);
  const [isMobile, setIsMobile] = useState(false);

  useEffect(() => {
    const checkMobile = () => {
      setIsMobile(window.innerWidth < 768);
      if (window.innerWidth < 768) {
        setIsCollapsed(true);
      }
    };

    checkMobile();
    window.addEventListener('resize', checkMobile);
    return () => window.removeEventListener('resize', checkMobile);
  }, []);

  const links = [
    { href: "/dashboard", label: "Dashboard", icon: "📊" },
    { href: "/dashboard/students", label: "Students", icon: "👨‍🎓" },
    { href: "/dashboard/teachers", label: "Teachers", icon: "👨‍🏫" },
    { href: "/dashboard/classes", label: "Classes", icon: "🏫" },
    { href: "/dashboard/finance", label: "Finance", icon: "💰" },
    { href: "/dashboard/analytics", label: "Analytics", icon: "📈" },
    { href: "/dashboard/notifications", label: "Notifications", icon: "🔔" },
    { href: "/dashboard/settings", label: "Settings", icon: "⚙️" },
  ];

  if (isMobile && !isCollapsed) {
    return null; // Hide sidebar on mobile when not collapsed
  }

  return (
    <>
      {isMobile && (
        <div
          className="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
          onClick={() => setIsCollapsed(true)}
        />
      )}
      <div className={`bg-gray-800 text-white h-full transition-all duration-300 z-50 ${
        isMobile ? 'fixed left-0 top-0' : 'relative'
      } ${isCollapsed ? 'w-16' : 'w-64'}`}>
        <div className="p-4 flex justify-between items-center">
          {!isCollapsed && <h2 className="text-xl font-bold">SaaS Dashboard</h2>}
          <button
            onClick={() => setIsCollapsed(!isCollapsed)}
            className="text-white hover:text-gray-300 p-1"
          >
            {isCollapsed ? (isMobile ? '☰' : '▶') : '◀'}
          </button>
        </div>
        <nav className="mt-4">
          {links.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              className={`flex items-center px-4 py-2 hover:bg-gray-700 transition-colors ${
                pathname === link.href ? "bg-gray-700" : ""
              } ${isCollapsed ? 'justify-center' : ''}`}
              title={isCollapsed ? link.label : ''}
              onClick={() => isMobile && setIsCollapsed(true)}
            >
              <span className="text-lg">{link.icon}</span>
              {!isCollapsed && <span className="ml-3">{link.label}</span>}
            </Link>
          ))}
        </nav>
      </div>
    </>
  );
};

export default Sidebar;