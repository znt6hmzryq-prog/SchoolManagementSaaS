"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";
import { Card } from "@/components/ui/Card";
import { Table } from "@/components/ui/Table";
import { Spinner } from "@/components/ui/Spinner";

export default function FinancePage() {
  const { data: invoices, isLoading: invoicesLoading } = useQuery({
    queryKey: ["invoices"],
    queryFn: () => api.get("/invoices").then((res) => res.data),
  });

  const { data: payments, isLoading: paymentsLoading } = useQuery({
    queryKey: ["payments"],
    queryFn: () => api.get("/payments").then((res) => res.data),
  });

  const { data: financial, isLoading: financialLoading } = useQuery({
    queryKey: ["financial"],
    queryFn: () => api.get("/dashboard/financial").then((res) => res.data),
  });

  if (invoicesLoading || paymentsLoading || financialLoading) {
    return <div className="flex justify-center items-center h-64"><Spinner size="lg" /></div>;
  }

  const totalRevenue = financial?.total_revenue || 0;
  const pendingInvoices = financial?.pending_invoices || 0;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">Finance</h1>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <Card>
          <h2 className="text-lg font-semibold mb-2">Total Revenue</h2>
          <p className="text-2xl font-bold text-green-600">${totalRevenue}</p>
        </Card>
        <Card>
          <h2 className="text-lg font-semibold mb-2">Pending Invoices</h2>
          <p className="text-2xl font-bold text-yellow-600">{pendingInvoices}</p>
        </Card>
        <Card>
          <h2 className="text-lg font-semibold mb-2">Subscription Status</h2>
          <p className="text-lg">Active</p>
        </Card>
      </div>

      <div className="mb-8">
        <h2 className="text-xl font-semibold mb-4">Recent Invoices</h2>
        <Table headers={["ID", "Student", "Amount", "Paid", "Balance", "Status", "Due Date"]}>
          {invoices?.data?.slice(0, 10).map((invoice: any) => (
            <tr key={invoice.id}>
              <td className="px-6 py-4 whitespace-nowrap">{invoice.id}</td>
              <td className="px-6 py-4 whitespace-nowrap">{invoice.student?.first_name} {invoice.student?.last_name}</td>
              <td className="px-6 py-4 whitespace-nowrap">${invoice.amount}</td>
              <td className="px-6 py-4 whitespace-nowrap">${invoice.paid_amount}</td>
              <td className="px-6 py-4 whitespace-nowrap">${invoice.balance}</td>
              <td className="px-6 py-4 whitespace-nowrap">{invoice.status}</td>
              <td className="px-6 py-4 whitespace-nowrap">{invoice.due_date}</td>
            </tr>
          ))}
        </Table>
      </div>

      <div>
        <h2 className="text-xl font-semibold mb-4">Recent Payments</h2>
        <Table headers={["ID", "Invoice ID", "Amount", "Date"]}>
          {payments?.slice(0, 10).map((payment: any) => (
            <tr key={payment.id}>
              <td className="px-6 py-4 whitespace-nowrap">{payment.id}</td>
              <td className="px-6 py-4 whitespace-nowrap">{payment.invoice_id}</td>
              <td className="px-6 py-4 whitespace-nowrap">${payment.amount}</td>
              <td className="px-6 py-4 whitespace-nowrap">{payment.created_at}</td>
            </tr>
          ))}
        </Table>
      </div>
    </div>
  );
}