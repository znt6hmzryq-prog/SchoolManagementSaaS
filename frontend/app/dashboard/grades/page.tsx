"use client";

import React from "react";
import Layout from "@/components/dashboard/Layout";
import DataTable from "@/components/dashboard/DataTable";
import ModalForm from "@/components/dashboard/ModalForm";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { getGrades, submitGrade } from "@/services/api";

export default function GradesPage() {
  const qc = useQueryClient();
  const { data: grades = [] } = useQuery({ queryKey: ["grades"], queryFn: getGrades });
  const [open, setOpen] = React.useState(false);

  const mutation = useMutation({
    mutationFn: submitGrade,
    onSuccess: () => qc.invalidateQueries({ queryKey: ["grades"] }),
  });

  React.useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) window.location.href = "/login";
  }, []);

  return (
    <Layout>
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-xl font-semibold">Grades</h2>
        <button onClick={()=>setOpen(true)} className="px-3 py-2 bg-blue-600 text-white rounded">Add Grade</button>
      </div>

      <DataTable
        columns={[
          { key: "student", label: "Student", render: (r:any)=> r.student ? `${r.student.first_name} ${r.student.last_name}` : '-' },
          { key: "subject", label: "Subject" },
          { key: "score", label: "Score" },
          { key: "exam_date", label: "Exam Date" },
        ]}
        data={grades}
      />

      <ModalForm open={open} onClose={()=>setOpen(false)} title="Add Grade">
        <GradeForm onSubmit={(payload:any)=>{ mutation.mutate(payload, { onSuccess: ()=>setOpen(false) }) }} />
      </ModalForm>
    </Layout>
  );
}

function GradeForm({ onSubmit }: any) {
  const [student_id, setStudentId] = React.useState("");
  const [subject, setSubject] = React.useState("");
  const [score, setScore] = React.useState("");
  const [max_score, setMax] = React.useState("");
  const [exam_date, setDate] = React.useState("");

  return (
    <form onSubmit={(e)=>{ e.preventDefault(); onSubmit({ student_id, subject, score, max_score, exam_date }); }} className="space-y-3">
      <input required placeholder="Student ID" className="w-full p-2 border" value={student_id} onChange={e=>setStudentId(e.target.value)}/>
      <input required placeholder="Subject" className="w-full p-2 border" value={subject} onChange={e=>setSubject(e.target.value)}/>
      <input required placeholder="Score" className="w-full p-2 border" value={score} onChange={e=>setScore(e.target.value)}/>
      <input required placeholder="Max Score" className="w-full p-2 border" value={max_score} onChange={e=>setMax(e.target.value)}/>
      <input required type="date" className="w-full p-2 border" value={exam_date} onChange={e=>setDate(e.target.value)}/>
      <div className="flex justify-end"><button type="submit" className="px-4 py-2 bg-blue-600 text-white rounded">Create</button></div>
    </form>
  );
}
