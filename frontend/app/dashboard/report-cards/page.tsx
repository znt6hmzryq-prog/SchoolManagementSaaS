"use client";

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { getStudents } from '../../../services/api';
import ReportCardTable from '../../../components/ReportCardTable';

export default function ReportCardsPage() {
  const { data, isLoading, error } = useQuery({ queryKey: ['students'], queryFn: getStudents });

  return (
    <div className="p-6">
      <h1 className="text-2xl font-semibold mb-4">Report Cards</h1>
      {isLoading && <p>Loading students...</p>}
      {error && <p className="text-red-600">Failed to load students</p>}
      {data && <ReportCardTable students={data} />}
    </div>
  );
}
