"use client";

import React from "react";
import { motion } from "framer-motion";
import { Card } from "@/components/ui/Card";

const testimonials = [
  { quote: 'This platform saved us hours every week.', author: 'Sarah Johnson, Principal' },
  { quote: 'Parent communication has never been easier.', author: 'Michael Chen, IT Director' },
  { quote: 'Finance and reporting are effortless now.', author: 'Dr. Emily Rodriguez, Superintendent' },
];

export default function Testimonials() {
  return (
    <section className="py-20 bg-white">
      <div className="max-w-6xl mx-auto px-4">
        <div className="text-center mb-8">
          <motion.h3 initial={{ y: 6, opacity: 0 }} animate={{ y: 0, opacity: 1 }} className="text-2xl font-bold">What schools say</motion.h3>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {testimonials.map((t, idx) => (
            <motion.div key={idx} initial={{ y: 8, opacity: 0 }} animate={{ y: 0, opacity: 1 }} transition={{ delay: idx * 0.08 }}>
              <Card className="p-6">
                <p className="text-gray-700">“{t.quote}”</p>
                <div className="mt-4 font-semibold">{t.author}</div>
              </Card>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
}
