"use client";

import Link from "next/link";
import { Button } from "@/components/ui/Button";
import { Card } from "@/components/ui/Card";
import dynamic from "next/dynamic";
import { ArrowRight, CheckCircle, Star, Users, TrendingUp, Shield } from "lucide-react";

import Hero from "@/components/landing/Hero";
import Features from "@/components/landing/Features";
import PricingPreview from "@/components/landing/PricingPreview";
import Testimonials from "@/components/landing/Testimonials";
import CTA from "@/components/landing/CTA";

const ClientHero = dynamic(() => import("@/components/ClientHero"), { ssr: false });

export default function HomePage() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
      <Hero />

      <Features />

      {/* Product Preview Section */}
      <section className="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div className="max-w-7xl mx-auto text-center">
          <h2 className="text-3xl font-bold text-gray-900 mb-12">See It In Action</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <Card className="p-6">
              <h3 className="text-xl font-semibold mb-4">Admin Dashboard</h3>
              <div className="h-48 bg-blue-100 rounded-lg flex items-center justify-center">
                <span className="text-blue-600 font-medium">Dashboard Preview</span>
              </div>
            </Card>
            <Card className="p-6">
              <h3 className="text-xl font-semibold mb-4">Teacher Panel</h3>
              <div className="h-48 bg-green-100 rounded-lg flex items-center justify-center">
                <span className="text-green-600 font-medium">Teacher Interface</span>
              </div>
            </Card>
            <Card className="p-6">
              <h3 className="text-xl font-semibold mb-4">Student Portal</h3>
              <div className="h-48 bg-purple-100 rounded-lg flex items-center justify-center">
                <span className="text-purple-600 font-medium">Student Access</span>
              </div>
            </Card>
          </div>
          <div className="mt-12">
            <Link href="/demo">
              <Button size="lg" className="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3">
                Try Live Demo
              </Button>
            </Link>
          </div>
        </div>
      </section>

      {/* Pricing Section */}
      <section id="pricing" className="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <PricingPreview />
      </section>

      <Testimonials />

      {/* FAQ Section */}
      <section className="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div className="max-w-4xl mx-auto">
          <h2 className="text-3xl font-bold text-center text-gray-900 mb-12">Frequently Asked Questions</h2>
          <div className="space-y-6">
            {[
              { q: "How does the system work?", a: "Our SaaS platform provides a complete school management solution accessible from any device with internet." },
              { q: "How long is the free trial?", a: "We offer a 30-day free trial with full access to all features. No credit card required." },
              { q: "Do you provide support?", a: "Yes, we offer email support for all plans, with priority support and phone support for Professional and Enterprise plans." },
              { q: "Can schools manage multiple campuses?", a: "Yes, our multi-tenant architecture supports multiple schools and campuses within one account." },
            ].map((faq, index) => (
              <Card key={index} className="p-6">
                <h3 className="text-lg font-semibold mb-2">{faq.q}</h3>
                <p className="text-gray-600">{faq.a}</p>
              </Card>
            ))}
          </div>
        </div>
      </section>

      <CTA />

      {/* Footer */}
      <footer className="bg-gray-900 text-white py-12 px-4 sm:px-6 lg:px-8">
        <div className="max-w-7xl mx-auto">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
              <h3 className="text-lg font-semibold mb-4">SchoolManagement SaaS</h3>
              <p className="text-gray-400">The complete school management solution for the modern era.</p>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Product</h4>
              <ul className="space-y-2 text-gray-400">
                <li><Link href="#features">Features</Link></li>
                <li><Link href="#pricing">Pricing</Link></li>
                <li><Link href="/demo">Demo</Link></li>
              </ul>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Company</h4>
              <ul className="space-y-2 text-gray-400">
                <li><Link href="/about">About</Link></li>
                <li><Link href="/contact">Contact</Link></li>
                <li><Link href="/blog">Blog</Link></li>
              </ul>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Legal</h4>
              <ul className="space-y-2 text-gray-400">
                <li><Link href="/privacy">Privacy Policy</Link></li>
                <li><Link href="/terms">Terms of Service</Link></li>
              </ul>
            </div>
          </div>
          <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2026 SchoolManagement SaaS. All rights reserved.</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
