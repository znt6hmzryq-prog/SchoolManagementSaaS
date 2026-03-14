"use client";

import React from "react";
import Layout from "@/components/dashboard/Layout";
import DataTable from "@/components/dashboard/DataTable";
import ModalForm from "@/components/dashboard/ModalForm";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { getStudents, createStudent } from "@/services/api";

export default function StudentsPage() {
  const qc = useQueryClient();
  const { data: students = [] } = useQuery({ queryKey: ["students"], queryFn: getStudents });
  const [open, setOpen] = React.useState(false);
  const [toast, setToast] = React.useState<string | null>(null);

  const mutation = useMutation({
    mutationFn: createStudent,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ["students"] });
      setToast("Student created successfully");
    },
  });

  React.useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) window.location.href = "/login";
  }, []);

  return (
    <Layout>
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-xl font-semibold">Students</h2>
        <button
          onClick={() => setOpen(true)}
          className="px-3 py-2 bg-blue-600 text-white rounded"
        >
          Add Student
        </button>
      </div>

      <DataTable
        columns={[
          { key: "first_name", label: "First Name" },
          { key: "last_name", label: "Last Name" },
          { key: "email", label: "Email" },
          { key: "phone", label: "Phone" },
          { key: "section", label: "Class", render: (r: any) => r.section?.classRoom?.name || "-" },
        ]}
        data={students}
      />

      <ModalForm open={open} onClose={() => setOpen(false)} title="Add Student">
        <StudentForm
          onSubmit={(payload: any) => {
            mutation.mutate(payload, { onSuccess: () => setOpen(false) });
          }}
        />
      </ModalForm>

      {toast && (
        <div className="fixed right-4 bottom-4 bg-green-600 text-white px-4 py-2 rounded shadow">
          {toast}
          <button className="ml-3 underline" onClick={() => setToast(null)}>Close</button>
        </div>
      )}
    </Layout>
  );
}

function StudentForm({ onSubmit }: any) {
  const [first_name, setFirstName] = React.useState("");
  const [last_name, setLastName] = React.useState("");
  const [email, setEmail] = React.useState("");
  const [phone, setPhone] = React.useState("");
  const [class_id, setClassId] = React.useState("");

  return (
    <form
      onSubmit={(e) => {
        e.preventDefault();
        onSubmit({ first_name, last_name, email, phone, class_id });
      }}
      className="space-y-3"
    >
      <input required placeholder="First name" className="w-full p-2 border" value={first_name} onChange={e=>setFirstName(e.target.value)}/>
      <input required placeholder="Last name" className="w-full p-2 border" value={last_name} onChange={e=>setLastName(e.target.value)}/>
      <input placeholder="Email" className="w-full p-2 border" value={email} onChange={e=>setEmail(e.target.value)}/>
      <input placeholder="Phone" className="w-full p-2 border" value={phone} onChange={e=>setPhone(e.target.value)}/>
      <input placeholder="Class ID" className="w-full p-2 border" value={class_id} onChange={e=>setClassId(e.target.value)}/>

      <div className="flex justify-end">
        <button type="submit" className="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
      </div>
    </form>
  );
}
