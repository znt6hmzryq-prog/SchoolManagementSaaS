"use client";

import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { getClassPerformance, getStudentTrends, getAttendanceTrends, getAIInsights } from '../../../services/api';
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  Tooltip,
  ResponsiveContainer,
  LineChart,
  Line,
  AreaChart,
  Area,
  CartesianGrid,
} from 'recharts';

export default function AnalyticsPage() {
  const { data: classPerfData, isLoading: loadingPerf } = useQuery({ queryKey: ['analytics','class-performance'], queryFn: getClassPerformance });
  const { data: studentTrendsData, isLoading: loadingTrends } = useQuery({ queryKey: ['analytics','student-trends'], queryFn: getStudentTrends });
  const { data: attendanceData, isLoading: loadingAttendance } = useQuery({ queryKey: ['analytics','attendance-trends'], queryFn: getAttendanceTrends });
  const { data: aiInsightsData, isLoading: loadingInsights } = useQuery({ queryKey: ['analytics','ai-insights'], queryFn: getAIInsights });

  const subjects = classPerfData?.subjects || [];
  const trend = studentTrendsData?.trend || [];
  const attendance = attendanceData?.attendance || [];
  const insights = aiInsightsData?.insights || [];

  return (
    <div className="p-6">
      <h1 className="text-2xl font-semibold mb-4 text-white">Analytics</h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div className="col-span-2 bg-gray-800 p-4 rounded">
          <h2 className="text-lg font-medium mb-2">Class Performance</h2>
          {loadingPerf ? (
            <div className="text-gray-400">Loading...</div>
          ) : (
            <div style={{ width: '100%', height: 300 }}>
              <ResponsiveContainer>
                <BarChart data={subjects}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" />
                  <YAxis />
                  <Tooltip />
                  <Bar dataKey="average" fill="#2563EB" />
                </BarChart>
              </ResponsiveContainer>
            </div>
          )}
        </div>

        <div className="bg-gray-800 p-4 rounded">
          <h2 className="text-lg font-medium mb-2">AI Insights</h2>
          {loadingInsights ? (
            <div className="text-gray-400">Loading...</div>
          ) : (
            <div className="space-y-2">
              {insights.length === 0 && <div className="text-gray-400">No insights available.</div>}
              {insights.map((ins: string, idx: number) => (
                <div key={idx} className="bg-gray-900 p-3 rounded text-sm">{ins}</div>
              ))}
            </div>
          )}
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div className="bg-gray-800 p-4 rounded">
          <h3 className="text-lg font-medium mb-2">Student Trends</h3>
          {loadingTrends ? (
            <div className="text-gray-400">Loading...</div>
          ) : (
            <div style={{ width: '100%', height: 300 }}>
              <ResponsiveContainer>
                <LineChart data={trend}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="month" />
                  <YAxis />
                  <Tooltip />
                  <Line type="monotone" dataKey="average" stroke="#10B981" strokeWidth={2} />
                </LineChart>
              </ResponsiveContainer>
            </div>
          )}
        </div>

        <div className="bg-gray-800 p-4 rounded">
          <h3 className="text-lg font-medium mb-2">Attendance Trends</h3>
          {loadingAttendance ? (
            <div className="text-gray-400">Loading...</div>
          ) : (
            <div style={{ width: '100%', height: 300 }}>
              <ResponsiveContainer>
                <AreaChart data={attendance}>
                  <defs>
                    <linearGradient id="colorPresent" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="5%" stopColor="#F59E0B" stopOpacity={0.8} />
                      <stop offset="95%" stopColor="#F59E0B" stopOpacity={0} />
                    </linearGradient>
                  </defs>
                  <XAxis dataKey="date" />
                  <YAxis />
                  <Tooltip />
                  <Area type="monotone" dataKey="present" stroke="#F59E0B" fillOpacity={1} fill="url(#colorPresent)" />
                </AreaChart>
              </ResponsiveContainer>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
