"use client";

import React from "react";
import Layout from "@/components/dashboard/Layout";
import DataTable from "@/components/dashboard/DataTable";
import ModalForm from "@/components/dashboard/ModalForm";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { getAttendance, markAttendance, getStudents } from "@/services/api";

export default function AttendancePage() {
  const qc = useQueryClient();
  const { data: attendance = [] } = useQuery({ queryKey: ["attendance"], queryFn: getAttendance });
  const [open, setOpen] = React.useState(false);

  const mutation = useMutation({
    mutationFn: async (payloads: any[]) => {
      await Promise.all(payloads.map((p) => markAttendance(p)));
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: ["attendance"] }),
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
        <AttendForm onSubmit={(payloads:any[])=>{ mutation.mutate(payloads, { onSuccess: ()=>setOpen(false) }); }} />
      </ModalForm>
    </Layout>
  );
}

function AttendForm({ onSubmit }: any) {
  const { data: students = [] } = useQuery({ queryKey: ["students"], queryFn: getStudents });
  const [rows, setRows] = React.useState<any[]>([]);

  React.useEffect(() => {
    const today = new Date().toISOString().slice(0, 10);
    if (Array.isArray(students)) {
      setRows(
        students.map((s: any) => ({ student_id: s.id, attendance_date: today, status: "present" }))
      );
    }
  }, [students]);

  const updateRow = (idx: number, changes: any) => {
    setRows((r) => r.map((row, i) => (i === idx ? { ...row, ...changes } : row)));
  };

  return (
    <form
      onSubmit={(e) => {
        e.preventDefault();
        // submit all rows
        onSubmit(rows);
      }}
      className="space-y-3"
    >
      <div className="max-h-64 overflow-y-auto border rounded">
        <table className="w-full text-left">
          <thead>
            <tr className="bg-gray-100">
              <th className="p-2">Student</th>
              <th className="p-2">Date</th>
              <th className="p-2">Status</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((row, idx) => {
              const student = students.find((s: any) => s.id === row.student_id);
              return (
                <tr key={row.student_id} className="border-t">
                  <td className="p-2">{student ? `${student.first_name} ${student.last_name}` : row.student_id}</td>
                  <td className="p-2">
                    <input type="date" className="p-1 border" value={row.attendance_date} onChange={(e)=>updateRow(idx, { attendance_date: e.target.value })} />
                  </td>
                  <td className="p-2">
                    <select className="p-1 border" value={row.status} onChange={(e)=>updateRow(idx, { status: e.target.value })}>
                      <option value="present">Present</option>
                      <option value="absent">Absent</option>
                      <option value="late">Late</option>
                    </select>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>

      <div className="flex justify-end"><button type="submit" className="px-4 py-2 bg-blue-600 text-white rounded">Submit Attendance</button></div>
    </form>
  );
}
