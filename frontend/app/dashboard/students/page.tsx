"use client";

import React from "react";
import Layout from "@/components/dashboard/Layout";
import DataTable from "@/components/dashboard/DataTable";
import ModalForm from "@/components/dashboard/ModalForm";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { getStudents, createStudent } from "@/services/api";

export default function StudentsPage() {
  const qc = useQueryClient();
  const { data: students = [] } = useQuery(["students"], getStudents);
  const [open, setOpen] = React.useState(false);

  const mutation = useMutation(createStudent, {
    onSuccess: () => qc.invalidateQueries(["students"]),
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
    </Layout>
  );
}

function StudentForm({ onSubmit }: any) {
  const [first_name, setFirstName] = React.useState("");
  const [last_name, setLastName] = React.useState("");
  const [email, setEmail] = React.useState("");
  const [phone, setPhone] = React.useState("");
  const [section_id, setSectionId] = React.useState("");

  return (
    <form
      onSubmit={(e) => {
        e.preventDefault();
        onSubmit({ first_name, last_name, email, phone, section_id });
      }}
      className="space-y-3"
    >
      <input required placeholder="First name" className="w-full p-2 border" value={first_name} onChange={e=>setFirstName(e.target.value)}/>
      <input required placeholder="Last name" className="w-full p-2 border" value={last_name} onChange={e=>setLastName(e.target.value)}/>
      <input placeholder="Email" className="w-full p-2 border" value={email} onChange={e=>setEmail(e.target.value)}/>
      <input placeholder="Phone" className="w-full p-2 border" value={phone} onChange={e=>setPhone(e.target.value)}/>
      <input placeholder="Section ID" className="w-full p-2 border" value={section_id} onChange={e=>setSectionId(e.target.value)}/>

      <div className="flex justify-end">
        <button type="submit" className="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
      </div>
    </form>
  );
}
"use client";

import { useStudents } from "@/hooks/useStudents";
import { useState, useMemo } from "react";
import axios from "axios";
import { useQueryClient } from "@tanstack/react-query";
import { Button } from "@/components/ui/Button";
import { Modal } from "@/components/ui/Modal";
import { Input } from "@/components/ui/Input";
import { Select } from "@/components/ui/Select";
import { Table } from "@/components/ui/Table";
import { Pagination } from "@/components/ui/Pagination";
import { Spinner } from "@/components/ui/Spinner";
import { Toast } from "@/components/ui/Toast";

export default function StudentsPage() {
  const { data: students, isLoading } = useStudents();
  const queryClient = useQueryClient();

  const [showModal, setShowModal] = useState(false);
  const [editingStudent, setEditingStudent] = useState<any>(null);
  const [search, setSearch] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [toast, setToast] = useState<{ message: string; type: 'success' | 'error' } | null>(null);
  const itemsPerPage = 10;

  const [form, setForm] = useState({
    first_name: "",
    last_name: "",
    email: "",
    section_id: "",
  });

  const filteredStudents = useMemo(() => {
    if (!students) return [];
    return students.filter((student: any) =>
      `${student.first_name} ${student.last_name}`.toLowerCase().includes(search.toLowerCase()) ||
      student.email.toLowerCase().includes(search.toLowerCase())
    );
  }, [students, search]);

  const paginatedStudents = useMemo(() => {
    const start = (currentPage - 1) * itemsPerPage;
    return filteredStudents.slice(start, start + itemsPerPage);
  }, [filteredStudents, currentPage]);

  const totalPages = Math.ceil(filteredStudents.length / itemsPerPage);

  const handleSubmit = async (e: any) => {
    e.preventDefault();

    const token = localStorage.getItem("token");

    try {
      if (editingStudent) {
        await axios.put(
          `http://127.0.0.1:8000/api/students/${editingStudent.id}`,
          form,
          {
            headers: {
              Authorization: `Bearer ${token}`,
              Accept: "application/json",
            },
          }
        );
        setToast({ message: "Student updated successfully", type: "success" });
      } else {
        await axios.post(
          "http://127.0.0.1:8000/api/students",
          form,
          {
            headers: {
              Authorization: `Bearer ${token}`,
              Accept: "application/json",
            },
          }
        );
        setToast({ message: "Student created successfully", type: "success" });
      }

      queryClient.invalidateQueries({ queryKey: ["students"] });

      setShowModal(false);
      setEditingStudent(null);
      setForm({
        first_name: "",
        last_name: "",
        email: "",
        section_id: "",
      });
    } catch (error: any) {
      console.log("Error:", error.response?.data);
      setToast({ message: "Error saving student", type: "error" });
    }
  };

  const handleEdit = (student: any) => {
    setEditingStudent(student);
    setForm({
      first_name: student.first_name,
      last_name: student.last_name,
      email: student.email,
      section_id: student.section_id || "",
    });
    setShowModal(true);
  };

  const handleDelete = async (id: number) => {
    if (!confirm("Are you sure you want to delete this student?")) return;

    const token = localStorage.getItem("token");

    try {
      await axios.delete(`http://127.0.0.1:8000/api/students/${id}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      queryClient.invalidateQueries({ queryKey: ["students"] });
      setToast({ message: "Student deleted successfully", type: "success" });
    } catch (error: any) {
      console.log("Delete error:", error.response?.data);
      setToast({ message: "Error deleting student", type: "error" });
    }
  };

  const openAddModal = () => {
    setEditingStudent(null);
    setForm({
      first_name: "",
      last_name: "",
      email: "",
      section_id: "",
    });
    setShowModal(true);
  };

  if (isLoading) return <div className="flex justify-center items-center h-64"><Spinner size="lg" /></div>;

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Students</h1>
        <Button onClick={openAddModal}>Add Student</Button>
      </div>

      <div className="mb-4">
        <Input
          type="text"
          placeholder="Search students..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="max-w-sm"
        />
      </div>

      <Table headers={["ID", "First Name", "Last Name", "Email", "Section", "Actions"]}>
        {paginatedStudents.map((student: any) => (
          <tr key={student.id}>
            <td className="px-6 py-4 whitespace-nowrap">{student.id}</td>
            <td className="px-6 py-4 whitespace-nowrap">{student.first_name}</td>
            <td className="px-6 py-4 whitespace-nowrap">{student.last_name}</td>
            <td className="px-6 py-4 whitespace-nowrap">{student.email}</td>
            <td className="px-6 py-4 whitespace-nowrap">{student.section?.name || "N/A"}</td>
            <td className="px-6 py-4 whitespace-nowrap space-x-2">
              <Button size="sm" onClick={() => handleEdit(student)}>Edit</Button>
              <Button size="sm" variant="danger" onClick={() => handleDelete(student.id)}>Delete</Button>
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
        title={editingStudent ? "Edit Student" : "Add Student"}
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
            label="Section ID"
            value={form.section_id}
            onChange={(e) => setForm({ ...form, section_id: e.target.value })}
          />
          <div className="flex justify-end space-x-2 mt-4">
            <Button type="button" variant="secondary" onClick={() => setShowModal(false)}>
              Cancel
            </Button>
            <Button type="submit">{editingStudent ? "Update" : "Create"}</Button>
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