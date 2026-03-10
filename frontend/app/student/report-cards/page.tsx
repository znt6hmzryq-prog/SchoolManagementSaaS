"use client";

import { useQuery } from "@tanstack/react-query";
import api from "@/services/api";

export default function StudentReportCardsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ["student-report-cards"],
    queryFn: () => api.get("/student/report-cards").then((res) => res.data),
  });

  if (isLoading) return <div>Loading...</div>;

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">Report Cards</h1>
      <div>
        {Object.keys(data?.report_cards || {}).map((subject) => (
          <div key={subject} className="mb-4">
            <h2>{subject}</h2>
            {data.report_cards[subject].map((grade: any) => (
              <p key={grade.id}>Assessment: {grade.assessment?.title} - Grade: {grade.grade}</p>
            ))}
          </div>
        ))}
      </div>
    </div>
  );
}