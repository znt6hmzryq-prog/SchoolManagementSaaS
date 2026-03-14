"use client";

import React from "react";
import Link from "next/link";
import { createCheckout } from "@/services/api";
import { Button } from "@/components/ui/Button";
import { Card } from "@/components/ui/Card";

const plans = [
  {
    id: 'basic',
    name: 'Basic',
    price: '$29/mo',
    priceId: process.env.NEXT_PUBLIC_STRIPE_PRICE_BASIC || 'price_basic',
    features: ['Student management', 'Basic attendance', 'Email support'],
  },
  {
    id: 'pro',
    name: 'Pro',
    price: '$79/mo',
    priceId: process.env.NEXT_PUBLIC_STRIPE_PRICE_PRO || 'price_pro',
    features: ['Everything in Basic', 'Advanced analytics', 'Finance & billing', 'Priority support'],
  },
  {
    id: 'enterprise',
    name: 'Enterprise',
    price: 'Custom',
    priceId: process.env.NEXT_PUBLIC_STRIPE_PRICE_ENTERPRISE || 'price_enterprise',
    features: ['Custom integrations', 'Dedicated support', 'SLA'],
  },
];

export default function PricingPage() {
  const [loading, setLoading] = React.useState<string | null>(null);

  const handleSubscribe = async (priceId: string) => {
    try {
      setLoading(priceId);
      const res = await createCheckout({ price_id: priceId });
      if (res?.url) {
        window.location.href = res.url;
      } else if (res?.id && res?.url) {
        window.location.href = res.url;
      } else {
        // fallback
        alert('Failed to create checkout session');
      }
    } catch (err: any) {
      console.error(err);
      alert(err?.message || 'Checkout failed');
    } finally {
      setLoading(null);
    }
  };

  return (
    <main className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-12">
      <div className="max-w-6xl mx-auto px-4">
        <header className="text-center mb-12">
          <h1 className="text-4xl font-bold">Pricing</h1>
          <p className="text-gray-600 mt-2">Simple, predictable pricing for schools of all sizes.</p>
        </header>

        <section className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {plans.map((plan) => (
            <Card key={plan.id} className={`p-8 text-center ${plan.id === 'pro' ? 'border-2 border-blue-500 relative' : 'border'} `}>
              {plan.id === 'pro' && (
                <div className="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                  Most Popular
                </div>
              )}
              <h3 className="text-2xl font-bold mb-4">{plan.name}</h3>
              <div className="text-4xl font-bold text-blue-600 mb-2">{plan.price}</div>
              <ul className="text-left mt-4 mb-6 space-y-2">
                {plan.features.map((f, i) => (
                  <li key={i} className="text-gray-700">✓ {f}</li>
                ))}
              </ul>
              <div>
                <Button
                  className="w-full bg-blue-600 hover:bg-blue-700 text-white"
                  onClick={() => handleSubscribe(plan.priceId)}
                  disabled={loading !== null}
                >
                  {loading === plan.priceId ? 'Redirecting...' : 'Subscribe'}
                </Button>
              </div>
              {plan.id === 'enterprise' && (
                <div className="mt-3">
                  <Link href="/contact">
                    <a className="text-sm text-blue-600 underline">Contact Sales</a>
                  </Link>
                </div>
              )}
            </Card>
          ))}
        </section>
      </div>
    </main>
  );
}
