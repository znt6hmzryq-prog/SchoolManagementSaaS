import type { Metadata } from "next";
import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css";
import Providers from "./providers";

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
});

export const metadata: Metadata = {
  title: "School Management SaaS - AI-Powered School Administration Platform",
  description: "Streamline school operations with our comprehensive SaaS platform. Manage students, teachers, classes, and finances with AI assistance. Start your free trial today.",
  keywords: "school management, SaaS, education software, student management, teacher management, school administration, AI assistant",
  authors: [{ name: "School Management SaaS Team" }],
  creator: "School Management SaaS",
  publisher: "School Management SaaS",
  formatDetection: {
    email: false,
    address: false,
    telephone: false,
  },
  metadataBase: new URL("https://schoolmanagementsaas.com"),
  alternates: {
    canonical: "/",
  },
  openGraph: {
    title: "School Management SaaS - AI-Powered School Administration",
    description: "Transform your school operations with our comprehensive SaaS platform featuring AI assistance, real-time analytics, and seamless management tools.",
    url: "https://schoolmanagementsaas.com",
    siteName: "School Management SaaS",
    images: [
      {
        url: "/og-image.jpg",
        width: 1200,
        height: 630,
        alt: "School Management SaaS Dashboard",
      },
    ],
    locale: "en_US",
    type: "website",
  },
  twitter: {
    card: "summary_large_image",
    title: "School Management SaaS - AI-Powered School Administration",
    description: "Transform your school operations with our comprehensive SaaS platform featuring AI assistance and real-time analytics.",
    images: ["/og-image.jpg"],
    creator: "@schoolmanagementsaas",
  },
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      "max-video-preview": -1,
      "max-image-preview": "large",
      "max-snippet": -1,
    },
  },
  verification: {
    google: "your-google-site-verification-code",
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en">
      <body className={`${geistSans.variable} ${geistMono.variable} antialiased`}>
        <Providers>
          {children}
        </Providers>
      </body>
    </html>
  );
}