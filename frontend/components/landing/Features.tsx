"use client";

import React from "react";
import { motion } from "framer-motion";
import { Card } from "@/components/ui/Card";

const features = [
  { title: 'Student Management', desc: 'Profiles, enrollment, custom fields' },
  { title: 'Teacher Tools', desc: 'Assignments, schedules, performance' },
  { title: 'Attendance', desc: 'Easy marking, reports, exports' },
  { title: 'Analytics', desc: 'Class performance, trends, dashboards' },
  { title: 'Billing', desc: 'Invoices, subscriptions, payments' },
  { title: 'Notifications', desc: 'Parent and teacher notifications' },
];

export default function Features() {
  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-12">
          <motion.h2 initial={{ y: 8, opacity: 0 }} animate={{ y: 0, opacity: 1 }} transition={{ duration: 0.6 }} className="text-3xl font-bold">Powerful features for schools</motion.h2>
          <motion.p initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ delay: 0.2 }} className="mt-2 text-gray-600">Everything you need to run a modern school.</motion.p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {features.map((f, idx) => (
            <motion.div key={f.title} initial={{ y: 8, opacity: 0 }} animate={{ y: 0, opacity: 1 }} transition={{ delay: idx * 0.08 }}>
              <Card className="p-6 hover:shadow-lg">
                <div className="font-semibold text-lg mb-2">{f.title}</div>
                <div className="text-gray-600">{f.desc}</div>
              </Card>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
}
