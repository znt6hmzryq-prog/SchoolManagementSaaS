"use client";

import React from "react";
import Layout from "@/components/dashboard/Layout";
import DataTable from "@/components/dashboard/DataTable";
import ModalForm from "@/components/dashboard/ModalForm";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { getAttendance, markAttendance } from "@/services/api";

export default function AttendancePage() {
  const qc = useQueryClient();
  const { data: attendance = [] } = useQuery(["attendance"], getAttendance);
  const [open, setOpen] = React.useState(false);

  const mutation = useMutation(markAttendance, {
    onSuccess: () => qc.invalidateQueries(["attendance"]),
  });

  React.useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) window.location.href = "/login";
  }, []);

  return (
    <Layout>
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-xl font-semibold">Attendance</h2>
        <button onClick={()=>setOpen(true)} className="px-3 py-2 bg-blue-600 text-white rounded">Mark Attendance</button>
      </div>

      <DataTable
        columns={[
          { key: "student", label: "Student", render: (r:any)=> r.student ? `${r.student.first_name} ${r.student.last_name}` : '-' },
          { key: "attendance_date", label: "Date" },
          { key: "status", label: "Status" },
        ]}
        data={attendance}
      />

      <ModalForm open={open} onClose={()=>setOpen(false)} title="Mark Attendance">
        <AttendForm onSubmit={(payload:any)=>{ mutation.mutate(payload, { onSuccess: ()=>setOpen(false) }) }} />
      </ModalForm>
    </Layout>
  );
}

function AttendForm({ onSubmit }: any) {
  const [teaching_assignment_id, setTa] = React.useState("");
  const [student_id, setStudentId] = React.useState("");
  const [attendance_date, setDate] = React.useState("");
  const [status, setStatus] = React.useState("present");

  return (
    <form onSubmit={(e)=>{ e.preventDefault(); onSubmit({ teaching_assignment_id, student_id, attendance_date, status }); }} className="space-y-3">
      <input placeholder="Teaching Assignment ID" className="w-full p-2 border" value={teaching_assignment_id} onChange={e=>setTa(e.target.value)}/>
      <input required placeholder="Student ID" className="w-full p-2 border" value={student_id} onChange={e=>setStudentId(e.target.value)}/>
      <input required type="date" className="w-full p-2 border" value={attendance_date} onChange={e=>setDate(e.target.value)}/>
      <select className="w-full p-2 border" value={status} onChange={e=>setStatus(e.target.value)}>
        <option value="present">Present</option>
        <option value="absent">Absent</option>
        <option value="late">Late</option>
        <option value="excused">Excused</option>
      </select>
      <div className="flex justify-end"><button type="submit" className="px-4 py-2 bg-blue-600 text-white rounded">Submit</button></div>
    </form>
  );
}
