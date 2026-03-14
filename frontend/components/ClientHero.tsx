"use client";

import Link from "next/link";
import { Button } from "@/components/ui/Button";
import { Card } from "@/components/ui/Card";
import { motion } from "framer-motion";
import { ArrowRight, Users, TrendingUp, Shield } from "lucide-react";

export default function ClientHero() {
  return (
    <section className="relative py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10"></div>
      <div className="max-w-7xl mx-auto text-center relative z-10">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8 }}
        >
          <h1 className="text-5xl sm:text-7xl font-bold text-gray-900 mb-6 leading-tight">
            School
            <span className="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
              Management
            </span>
            <br />
            SaaS
          </h1>
          <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
            The complete school management solution for modern educational institutions.
            AI-powered insights, seamless automation, and enterprise-grade security.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center mb-12">
            <Link href="/demo">
              <Button size="lg" className="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 group">
                Try Live Demo
                <ArrowRight className="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" />
              </Button>
            </Link>
            <Link href="/signup">
              <Button size="lg" variant="secondary" className="px-8 py-4 rounded-full border-2 border-gray-300 hover:border-gray-400 transition-all duration-300">
                Start Free Trial
              </Button>
            </Link>
          </div>
        </motion.div>

        {/* Hero Illustration */}
        <motion.div
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 1, delay: 0.3 }}
          className="mt-16 flex justify-center"
        >
          <div className="relative">
            <div className="w-full max-w-5xl h-80 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-2xl shadow-2xl backdrop-blur-sm bg-opacity-20 border border-white/20 flex items-center justify-center">
              <div className="text-center text-white">
                <div className="grid grid-cols-3 gap-8 max-w-4xl mx-auto">
                  <motion.div
                    whileHover={{ scale: 1.05 }}
                    className="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20"
                  >
                    <Users className="w-8 h-8 mx-auto mb-3" />
                    <h3 className="font-semibold text-lg">Student Management</h3>
                    <p className="text-sm opacity-90">Complete profiles & tracking</p>
                  </motion.div>
                  <motion.div
                    whileHover={{ scale: 1.05 }}
                    className="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20"
                  >
                    <TrendingUp className="w-8 h-8 mx-auto mb-3" />
                    <h3 className="font-semibold text-lg">Analytics Dashboard</h3>
                    <p className="text-sm opacity-90">Real-time insights</p>
                  </motion.div>
                  <motion.div
                    whileHover={{ scale: 1.05 }}
                    className="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20"
                  >
                    <Shield className="w-8 h-8 mx-auto mb-3" />
                    <h3 className="font-semibold text-lg">Enterprise Security</h3>
                    <p className="text-sm opacity-90">Bank-level protection</p>
                  </motion.div>
                </div>
              </div>
            </div>
          </div>
        </motion.div>
      </div>
    </section>
  );
}
