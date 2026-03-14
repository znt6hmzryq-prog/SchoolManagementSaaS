"use client";

import Link from "next/link";
import { Button } from "@/components/ui/Button";
import { Card } from "@/components/ui/Card";
import dynamic from "next/dynamic";
import { ArrowRight, CheckCircle, Star, Users, TrendingUp, Shield } from "lucide-react";

const ClientHero = dynamic(() => import("@/components/ClientHero"), { ssr: false });

export default function HomePage() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
      <ClientHero />

      {/* Features Section */}
      <section className="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div className="max-w-7xl mx-auto">
          <h2 className="text-3xl font-bold text-center text-gray-900 mb-12">Powerful Features</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {[
              { icon: "👨‍🎓", title: "Student Management", desc: "Complete student profiles, enrollment, and tracking" },
              { icon: "👨‍🏫", title: "Teacher Management", desc: "Assign classes, track performance, manage schedules" },
              { icon: "📊", title: "Attendance Tracking", desc: "Automated attendance with reports and analytics" },
              { icon: "📈", title: "Grading System", desc: "Comprehensive grading with progress tracking" },
              { icon: "💰", title: "Finance & Billing", desc: "Invoice management, payments, and financial reports" },
              { icon: "📊", title: "Analytics Dashboard", desc: "Real-time insights and performance metrics" },
              { icon: "🔔", title: "Notifications", desc: "Automated alerts for parents, teachers, and admins" },
              { icon: "🏫", title: "Multi-School Support", desc: "Manage multiple campuses with tenant isolation" },
            ].map((feature, index) => (
              <Card key={index} className="text-center p-6 hover:shadow-lg transition-shadow">
                <div className="text-4xl mb-4">{feature.icon}</div>
                <h3 className="text-xl font-semibold mb-2">{feature.title}</h3>
                <p className="text-gray-600">{feature.desc}</p>
              </Card>
            ))}
          </div>
        </div>
      </section>

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
        <div className="max-w-7xl mx-auto">
          <h2 className="text-3xl font-bold text-center text-gray-900 mb-12">Choose Your Plan</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <Card className="p-8 text-center border-2 border-gray-200 hover:border-blue-500 transition-colors">
              <h3 className="text-2xl font-bold mb-4">Starter</h3>
              <div className="text-4xl font-bold text-blue-600 mb-2">$29<span className="text-lg text-gray-500">/month</span></div>
              <p className="text-gray-600 mb-6">Up to 200 students</p>
              <ul className="text-left mb-8 space-y-2">
                <li>✓ Student Management</li>
                <li>✓ Basic Attendance</li>
                <li>✓ Email Support</li>
                <li>✓ 1 School</li>
              </ul>
              <Button className="w-full">Start Free Trial</Button>
            </Card>
            <Card className="p-8 text-center border-2 border-blue-500 relative">
              <div className="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium">
                Most Popular
              </div>
              <h3 className="text-2xl font-bold mb-4">Professional</h3>
              <div className="text-4xl font-bold text-blue-600 mb-2">$79<span className="text-lg text-gray-500">/month</span></div>
              <p className="text-gray-600 mb-6">Unlimited students</p>
              <ul className="text-left mb-8 space-y-2">
                <li>✓ Everything in Starter</li>
                <li>✓ Advanced Analytics</li>
                <li>✓ Finance & Billing</li>
                <li>✓ Priority Support</li>
                <li>✓ Multiple Schools</li>
              </ul>
              <Button className="w-full bg-blue-600 hover:bg-blue-700">Start Free Trial</Button>
            </Card>
            <Card className="p-8 text-center border-2 border-gray-200 hover:border-blue-500 transition-colors">
              <h3 className="text-2xl font-bold mb-4">Enterprise</h3>
              <div className="text-4xl font-bold text-blue-600 mb-2">Custom</div>
              <p className="text-gray-600 mb-6">Tailored solutions</p>
              <ul className="text-left mb-8 space-y-2">
                <li>✓ Everything in Professional</li>
                <li>✓ Custom Integrations</li>
                <li>✓ Dedicated Support</li>
                <li>✓ On-premise Options</li>
                <li>✓ SLA Guarantee</li>
              </ul>
              <Button variant="secondary" className="w-full">Contact Sales</Button>
            </Card>
          </div>
        </div>
      </section>

      {/* Testimonials Section */}
      <section className="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div className="max-w-7xl mx-auto">
          <h2 className="text-3xl font-bold text-center text-gray-900 mb-12">What Schools Say</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <Card className="p-6">
              <p className="text-gray-600 mb-4">"This system has transformed how we manage our school. The automation saves us hours every week."</p>
              <div className="font-semibold">Sarah Johnson</div>
              <div className="text-sm text-gray-500">Principal, Lincoln High School</div>
            </Card>
            <Card className="p-6">
              <p className="text-gray-600 mb-4">"The parent portal keeps everyone informed. Communication has never been better."</p>
              <div className="font-semibold">Michael Chen</div>
              <div className="text-sm text-gray-500">IT Director, Maple Elementary</div>
            </Card>
            <Card className="p-6">
              <p className="text-gray-600 mb-4">"Financial management is now effortless. We can focus on education instead of paperwork."</p>
              <div className="font-semibold">Dr. Emily Rodriguez</div>
              <div className="text-sm text-gray-500">Superintendent, River Valley Schools</div>
            </Card>
          </div>
        </div>
      </section>

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

      {/* CTA Section */}
      <section className="py-20 px-4 sm:px-6 lg:px-8 bg-blue-600 text-white">
        <div className="max-w-4xl mx-auto text-center">
          <h2 className="text-3xl font-bold mb-6">Ready to Transform Your School?</h2>
          <p className="text-xl mb-8">Join thousands of schools already using SchoolManagement SaaS</p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/demo">
              <Button size="lg" className="bg-white text-blue-600 hover:bg-gray-100 px-8 py-3">
                Start Free Trial
              </Button>
            </Link>
            <Link href="/contact">
              <Button size="lg" variant="secondary" className="px-8 py-3">
                Book Demo
              </Button>
            </Link>
          </div>
        </div>
      </section>

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
