"use client";

import React from "react";
import { motion } from "framer-motion";
import Link from "next/link";
import { Button } from "@/components/ui/Button";
import { Card } from "@/components/ui/Card";

export default function PricingPreview() {
  return (
    <section className="py-20 bg-gray-50">
      <div className="max-w-6xl mx-auto px-4">
        <div className="text-center mb-8">
          <motion.h3 initial={{ y: 6, opacity: 0 }} animate={{ y: 0, opacity: 1 }} transition={{ duration: 0.5 }} className="text-2xl font-bold">Simple pricing</motion.h3>
          <motion.p initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ delay: 0.1 }} className="text-gray-600">Choose a plan that scales with your school.</motion.p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <Card className="p-6 text-center border">
            <div className="text-lg font-semibold">Basic</div>
            <div className="text-3xl font-bold mt-4 text-blue-600">$29<span className="text-sm text-gray-500">/mo</span></div>
            <ul className="text-left mt-4 space-y-2 text-gray-700">
              <li>Student management</li>
              <li>Basic attendance</li>
              <li>Email support</li>
            </ul>
            <div className="mt-6">
              <Link href="/pricing"><Button className="w-full">Choose</Button></Link>
            </div>
          </Card>

          <Card className="p-6 text-center border-2 border-blue-500 relative">
            <div className="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white px-3 py-1 rounded-full text-sm">Popular</div>
            <div className="text-lg font-semibold">Pro</div>
            <div className="text-3xl font-bold mt-4 text-blue-600">$79<span className="text-sm text-gray-500">/mo</span></div>
            <ul className="text-left mt-4 space-y-2 text-gray-700">
              <li>Advanced analytics</li>
              <li>Unlimited students</li>
              <li>Priority support</li>
            </ul>
            <div className="mt-6">
              <Link href="/pricing"><Button className="w-full bg-blue-600 text-white">Choose</Button></Link>
            </div>
          </Card>

          <Card className="p-6 text-center border">
            <div className="text-lg font-semibold">Enterprise</div>
            <div className="text-3xl font-bold mt-4 text-blue-600">Custom</div>
            <ul className="text-left mt-4 space-y-2 text-gray-700">
              <li>Custom integrations</li>
              <li>Dedicated support</li>
              <li>SLA</li>
            </ul>
            <div className="mt-6">
              <Link href="/contact"><Button className="w-full variant-secondary">Contact Sales</Button></Link>
            </div>
          </Card>
        </div>
      </div>
    </section>
  );
}
