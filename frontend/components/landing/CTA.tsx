"use client";

import React from "react";
import { motion } from "framer-motion";
import Link from "next/link";
import { Button } from "@/components/ui/Button";

export default function CTA() {
  return (
    <section className="py-20 bg-blue-600 text-white">
      <div className="max-w-4xl mx-auto px-4 text-center">
        <motion.h2 initial={{ y: 6, opacity: 0 }} animate={{ y: 0, opacity: 1 }} className="text-3xl font-bold">Ready to transform your school?</motion.h2>
        <motion.p initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ delay: 0.1 }} className="mt-3 mb-6">Join thousands of schools using our platform.</motion.p>
        <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ delay: 0.2 }} className="flex flex-col sm:flex-row gap-3 justify-center">
          <Link href="/pricing"><Button size="lg" className="bg-white text-blue-600">Start Free Trial</Button></Link>
          <Link href="/demo"><Button size="lg" variant="secondary">Try Live Demo</Button></Link>
        </motion.div>
      </div>
    </section>
  );
}
