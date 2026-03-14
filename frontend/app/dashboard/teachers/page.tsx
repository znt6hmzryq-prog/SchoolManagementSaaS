"use client";

import React, { useState, useMemo, useEffect } from "react";
import Layout from "@/components/dashboard/Layout";
import DataTable from "@/components/dashboard/DataTable";
import ModalForm from "@/components/dashboard/ModalForm";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import api, { getTeachers, createTeacher } from "@/services/api";

export default function TeachersPage() {
  const qc = useQueryClient();
  const { data: teachers = [], isLoading } = useQuery({ queryKey: ["teachers"], queryFn: getTeachers });
  const [showModal, setShowModal] = useState(false);
  const [editingTeacher, setEditingTeacher] = useState<any>(null);
  const [search, setSearch] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [toast, setToast] = useState<string | null>(null);
  const itemsPerPage = 10;

  useEffect(() => {
    const token = localStorage.getItem("token");
    if (!token) window.location.href = "/login";
  }, []);

  const [form, setForm] = useState({
    first_name: "",
    last_name: "",
    email: "",
    phone: "",
    subject: "",
  });

  const createMutation = useMutation({
    mutationFn: createTeacher,
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ["teachers"] });
      setToast("Teacher created successfully");
      setShowModal(false);
      resetForm();
    },
    onError: () => setToast("Error creating teacher"),
  });

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: any }) => api.put(`/teachers/${id}`, data),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ["teachers"] });
      setToast("Teacher updated successfully");
      setShowModal(false);
      setEditingTeacher(null);
      resetForm();
    },
    onError: () => setToast("Error updating teacher"),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => api.delete(`/teachers/${id}`),
    onSuccess: () => {
      qc.invalidateQueries({ queryKey: ["teachers"] });
      setToast("Teacher deleted successfully");
    },
    onError: () => setToast("Error deleting teacher"),
  });

  const filteredTeachers = useMemo(() => {
    if (!teachers) return [];
    return teachers.filter((teacher: any) =>
      `${teacher.first_name} ${teacher.last_name}`.toLowerCase().includes(search.toLowerCase()) ||
      teacher.email.toLowerCase().includes(search.toLowerCase())
    );
  }, [teachers, search]);

  const paginatedTeachers = useMemo(() => {
    const start = (currentPage - 1) * itemsPerPage;
    return filteredTeachers.slice(start, start + itemsPerPage);
  }, [filteredTeachers, currentPage]);

  const totalPages = Math.ceil(filteredTeachers.length / itemsPerPage);

  const handleSubmit = (e: any) => {
    e.preventDefault();
    if (editingTeacher) {
      updateMutation.mutate({ id: editingTeacher.id, data: form });
    } else {
      createMutation.mutate(form);
    }
  };

  const handleEdit = (teacher: any) => {
    setEditingTeacher(teacher);
    setForm({
      first_name: teacher.first_name,
      last_name: teacher.last_name,
      email: teacher.email,
      phone: teacher.phone || "",
      subject: teacher.subject || "",
    });
    setShowModal(true);
  };

  const handleDelete = (id: number) => {
    if (confirm("Are you sure you want to delete this teacher?")) {
      deleteMutation.mutate(id);
    }
  };

  const resetForm = () => {
    setForm({
      first_name: "",
      last_name: "",
      email: "",
      phone: "",
      subject: "",
    });
  };

  const openAddModal = () => {
    setEditingTeacher(null);
    resetForm();
    setShowModal(true);
  };

  if (isLoading) return <div className="flex justify-center items-center h-64">Loading...</div>;

  return (
    <Layout>
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Teachers</h1>
        <button onClick={openAddModal} className="px-3 py-2 bg-blue-600 text-white rounded">Add Teacher</button>
      </div>

      <div className="mb-4">
        <input
          type="text"
          placeholder="Search teachers..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="max-w-sm p-2 border"
        />
      </div>

      <div className="mb-6">
        <DataTable
          columns={[
            { key: "first_name", label: "First Name", render: (r: any) => `${r.first_name} ${r.last_name}` },
            { key: "email", label: "Email" },
            { key: "phone", label: "Phone" },
            { key: "subject", label: "Subject" },
            { key: "actions", label: "Actions", render: (r: any) => (
              <div className="space-x-2">
                <button onClick={() => handleEdit(r)} className="px-2 py-1 bg-gray-200 rounded">Edit</button>
                <button onClick={() => handleDelete(r.id)} className="px-2 py-1 bg-red-500 text-white rounded">Delete</button>
              </div>
            ) }
          ]}
          data={teachers}
        />
      </div>

      {/* ModalForm for add/edit teacher */}
      <ModalForm open={showModal} onClose={() => setShowModal(false)} title={editingTeacher ? "Edit Teacher" : "Add Teacher"}>
        <form onSubmit={handleSubmit} className="space-y-3">
          <input
            className="w-full p-2 border"
            placeholder="First name"
            value={form.first_name}
            onChange={(e) => setForm({ ...form, first_name: e.target.value })}
            required
          />
          <input
            className="w-full p-2 border"
            placeholder="Last name"
            value={form.last_name}
            onChange={(e) => setForm({ ...form, last_name: e.target.value })}
            required
          />
          <input
            className="w-full p-2 border"
            placeholder="Email"
            type="email"
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
          />
          <input
            className="w-full p-2 border"
            placeholder="Phone"
            value={form.phone}
            onChange={(e) => setForm({ ...form, phone: e.target.value })}
          />
          <input
            className="w-full p-2 border"
            placeholder="Subject"
            value={form.subject}
            onChange={(e) => setForm({ ...form, subject: e.target.value })}
          />

          <div className="flex justify-end">
            <button type="submit" className="px-4 py-2 bg-blue-600 text-white rounded">{editingTeacher ? "Update" : "Create"}</button>
          </div>
        </form>
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