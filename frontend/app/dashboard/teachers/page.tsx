"use client";

import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { getTeachers, createTeacher, updateTeacher, deleteTeacher } from "@/services/teacherService";
import { useState, useMemo } from "react";
import { Button } from "@/components/ui/Button";
import { Modal } from "@/components/ui/Modal";
import { Input } from "@/components/ui/Input";
import { Select } from "@/components/ui/Select";
import { Table } from "@/components/ui/Table";
import { Pagination } from "@/components/ui/Pagination";
import { Spinner } from "@/components/ui/Spinner";
import { Toast } from "@/components/ui/Toast";

export default function TeachersPage() {
  const queryClient = useQueryClient();
  const { data: teachers, isLoading } = useQuery({
    queryKey: ["teachers"],
    queryFn: getTeachers,
  });

  const [showModal, setShowModal] = useState(false);
  const [editingTeacher, setEditingTeacher] = useState<any>(null);
  const [search, setSearch] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [toast, setToast] = useState<{ message: string; type: 'success' | 'error' } | null>(null);
  const itemsPerPage = 10;

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
      queryClient.invalidateQueries({ queryKey: ["teachers"] });
      setToast({ message: "Teacher created successfully", type: "success" });
      setShowModal(false);
      resetForm();
    },
    onError: () => setToast({ message: "Error creating teacher", type: "error" }),
  });

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: any }) => updateTeacher(id, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["teachers"] });
      setToast({ message: "Teacher updated successfully", type: "success" });
      setShowModal(false);
      setEditingTeacher(null);
      resetForm();
    },
    onError: () => setToast({ message: "Error updating teacher", type: "error" }),
  });

  const deleteMutation = useMutation({
    mutationFn: deleteTeacher,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["teachers"] });
      setToast({ message: "Teacher deleted successfully", type: "success" });
    },
    onError: () => setToast({ message: "Error deleting teacher", type: "error" }),
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

  if (isLoading) return <div className="flex justify-center items-center h-64"><Spinner size="lg" /></div>;

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Teachers</h1>
        <Button onClick={openAddModal}>Add Teacher</Button>
      </div>

      <div className="mb-4">
        <Input
          type="text"
          placeholder="Search teachers..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="max-w-sm"
        />
      </div>

      <Table headers={["ID", "First Name", "Last Name", "Email", "Phone", "Subject", "Actions"]}>
        {paginatedTeachers.map((teacher: any) => (
          <tr key={teacher.id}>
            <td className="px-6 py-4 whitespace-nowrap">{teacher.id}</td>
            <td className="px-6 py-4 whitespace-nowrap">{teacher.first_name}</td>
            <td className="px-6 py-4 whitespace-nowrap">{teacher.last_name}</td>
            <td className="px-6 py-4 whitespace-nowrap">{teacher.email}</td>
            <td className="px-6 py-4 whitespace-nowrap">{teacher.phone}</td>
            <td className="px-6 py-4 whitespace-nowrap">{teacher.subject}</td>
            <td className="px-6 py-4 whitespace-nowrap space-x-2">
              <Button size="sm" onClick={() => handleEdit(teacher)}>Edit</Button>
              <Button size="sm" variant="danger" onClick={() => handleDelete(teacher.id)}>Delete</Button>
            </td>
          </tr>
        ))}
      </Table>

      {totalPages > 1 && (
        <Pagination
          currentPage={currentPage}
          totalPages={totalPages}
          onPageChange={setCurrentPage}
        />
      )}

      <Modal
        isOpen={showModal}
        onClose={() => setShowModal(false)}
        title={editingTeacher ? "Edit Teacher" : "Add Teacher"}
      >
        <form onSubmit={handleSubmit}>
          <Input
            label="First Name"
            value={form.first_name}
            onChange={(e) => setForm({ ...form, first_name: e.target.value })}
            required
          />
          <Input
            label="Last Name"
            value={form.last_name}
            onChange={(e) => setForm({ ...form, last_name: e.target.value })}
            required
          />
          <Input
            label="Email"
            type="email"
            value={form.email}
            onChange={(e) => setForm({ ...form, email: e.target.value })}
            required
          />
          <Input
            label="Phone"
            value={form.phone}
            onChange={(e) => setForm({ ...form, phone: e.target.value })}
          />
          <Input
            label="Subject"
            value={form.subject}
            onChange={(e) => setForm({ ...form, subject: e.target.value })}
          />
          <div className="flex justify-end space-x-2 mt-4">
            <Button type="button" variant="secondary" onClick={() => setShowModal(false)}>
              Cancel
            </Button>
            <Button type="submit">{editingTeacher ? "Update" : "Create"}</Button>
          </div>
        </form>
      </Modal>

      {toast && (
        <Toast
          message={toast.message}
          type={toast.type}
          onClose={() => setToast(null)}
        />
      )}
    </div>
  );
}