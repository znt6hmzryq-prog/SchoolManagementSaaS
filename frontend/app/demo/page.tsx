"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { login } from "@/services/auth";
import { Spinner } from "@/components/ui/Spinner";

export default function DemoPage() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const loginDemo = async () => {
      try {
        const data = await login("demo@school.com", "password");
        localStorage.setItem("token", data.token);
        localStorage.setItem("demoMode", "true"); // Flag for demo mode
        router.push("/dashboard");
      } catch (err: any) {
        setError("Demo login failed. Please try again.");
        setLoading(false);
      }
    };

    loginDemo();
  }, [router]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center">
          <Spinner size="lg" className="mb-4" />
          <p className="text-lg text-gray-600">Loading demo...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center">
          <p className="text-red-600 mb-4">{error}</p>
          <button
            onClick={() => window.location.reload()}
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return null;
}