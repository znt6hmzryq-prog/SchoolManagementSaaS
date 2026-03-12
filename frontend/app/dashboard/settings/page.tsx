"use client";

import React from "react";
import Layout from "@/components/dashboard/Layout";

export default function SettingsPage() {
  React.useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) window.location.href = "/login";
  }, []);

  return (
    <Layout>
      <h2 className="text-xl font-semibold mb-4">Settings</h2>
      <div className="bg-white rounded-lg p-6 shadow">Simple settings placeholder.</div>
    </Layout>
  );
}
export default function SettingsPage() {
  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">Settings</h1>
      <p>Settings management coming soon.</p>
    </div>
  );
}