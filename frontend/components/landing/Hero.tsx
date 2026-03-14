"use client";

import React from "react";
import { motion } from "framer-motion";
import Link from "next/link";
import { Button } from "@/components/ui/Button";

export default function Hero() {
  return (
    <section className="relative bg-gradient-to-br from-white to-blue-50 overflow-hidden">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
          <div>
            <motion.h1
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="text-4xl sm:text-5xl font-extrabold text-gray-900 leading-tight"
            >
              The modern school management platform
            </motion.h1>
            <motion.p
              initial={{ opacity: 0, y: 6 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8 }}
              className="mt-4 text-lg text-gray-600"
            >
              Manage students, teachers, attendance, and billing in one secure, scalable platform.
            </motion.p>
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ delay: 0.9 }}
              className="mt-8 flex flex-col sm:flex-row gap-3"
            >
              <Link href="/demo">
                <Button size="lg" className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3">
                  Try Live Demo
                </Button>
              </Link>
              <Link href="/pricing">
                <Button size="lg" variant="secondary" className="px-6 py-3">
                  Start Free Trial
                </Button>
              </Link>
            </motion.div>
          </div>

          <div className="relative">
            <motion.div
              initial={{ scale: 0.98, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              transition={{ duration: 0.7 }}
              className="bg-white rounded-2xl shadow-xl p-6"
            >
              <div className="h-64 md:h-72 lg:h-80 bg-gradient-to-br from-sky-50 to-indigo-50 rounded-xl flex items-center justify-center text-gray-500">
                <div className="text-center">
                  <div className="text-sm uppercase tracking-wide text-gray-400">Admin Dashboard</div>
                  <div className="mt-3 font-medium text-gray-700">Realtime analytics · Attendance · Grades</div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </div>
    </section>
  );
}
