"use client";

import React from "react";
import Layout from "@/components/dashboard/Layout";
import DataTable from "@/components/dashboard/DataTable";
import ModalForm from "@/components/dashboard/ModalForm";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { getClasses, createClass, getTeachers } from "@/services/api";

export default function ClassesPage() {
  const qc = useQueryClient();
  const { data: classes = [] } = useQuery({ queryKey: ["classes"], queryFn: getClasses });
  const [open, setOpen] = React.useState(false);

  const mutation = useMutation({
    mutationFn: createClass,
    onSuccess: () => qc.invalidateQueries({ queryKey: ["classes"] }),
  });

  React.useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) window.location.href = "/login";
  }, []);

  return (
    <Layout>
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-xl font-semibold">Classes</h2>
        <button onClick={()=>setOpen(true)} className="px-3 py-2 bg-blue-600 text-white rounded">Create Class</button>
      </div>

      <DataTable
        columns={[
          { key: "name", label: "Class Name" },
          { key: "grade_level", label: "Grade Level" },
          { key: "teacher", label: "Teacher", render: (r:any)=> r.teacher ? `${r.teacher.first_name} ${r.teacher.last_name}` : '-' },
        ]}
        data={classes}
      />

      <ModalForm open={open} onClose={()=>setOpen(false)} title="Create Class">
        <ClassForm onSubmit={(payload:any)=>{ mutation.mutate(payload, { onSuccess: ()=>setOpen(false) }) }} />
      </ModalForm>
    </Layout>
  );
}

function ClassForm({ onSubmit }: any) {
  const [name, setName] = React.useState("");
  const [grade_level, setGradeLevel] = React.useState("");
  const [teacher_id, setTeacherId] = React.useState("");

  const { data: teachers = [] } = useQuery({ queryKey: ["teachers"], queryFn: getTeachers });

  return (
    <form onSubmit={(e)=>{ e.preventDefault(); onSubmit({ name, grade_level, teacher_id }); }} className="space-y-3">
      <input required placeholder="Class name" className="w-full p-2 border" value={name} onChange={e=>setName(e.target.value)}/>
      <input placeholder="Grade level" className="w-full p-2 border" value={grade_level} onChange={e=>setGradeLevel(e.target.value)}/>
      <select className="w-full p-2 border" value={teacher_id} onChange={e=>setTeacherId(e.target.value)}>
        <option value="">Select teacher</option>
        {Array.isArray(teachers) && teachers.map((t:any)=>(
          <option key={t.id} value={t.id}>{t.first_name} {t.last_name}</option>
        ))}
      </select>
      <div className="flex justify-end"><button type="submit" className="px-4 py-2 bg-blue-600 text-white rounded">Create</button></div>
    </form>
  );
}
