"use client";

import React from 'react';
import { useMutation } from '@tanstack/react-query';
import { generateReportCard, emailReportCard } from '../services/api';

export default function ReportCardTable({ students }: { students: any[] }) {
  const generateMutation: any = useMutation({ mutationFn: (id: number) => generateReportCard(id) });
  const emailMutation: any = useMutation({ mutationFn: (id: number) => emailReportCard(id) });

  const handleGenerate = async (id: number) => {
    const res = await generateMutation.mutateAsync(id);
    if (res?.pdf_url) {
      window.open(res.pdf_url, '_blank');
    }
  };

  const handleEmail = async (id: number) => {
    await emailMutation.mutateAsync(id);
    alert('Email sent (if configured)');
  };

  return (
    <div className="overflow-x-auto">
      <table className="min-w-full bg-white">
        <thead>
          <tr>
            <th className="px-4 py-2">Student</th>
            <th className="px-4 py-2">Class</th>
            <th className="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          {students.map((s: any) => (
            <tr key={s.id} className="border-t">
              <td className="px-4 py-2">{s.first_name} {s.last_name}</td>
              <td className="px-4 py-2">{s.section?.classRoom?.name ?? ''} - {s.section?.name ?? ''}</td>
              <td className="px-4 py-2">
                <button className="mr-2 bg-blue-600 text-white px-3 py-1 rounded" onClick={() => handleGenerate(s.id)}>
                  {generateMutation.isLoading ? 'Generating...' : 'Generate'}
                </button>
                <button className="mr-2 bg-gray-600 text-white px-3 py-1 rounded" onClick={() => handleEmail(s.id)}>
                  {emailMutation.isLoading ? 'Sending...' : 'Send to Parent'}
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
