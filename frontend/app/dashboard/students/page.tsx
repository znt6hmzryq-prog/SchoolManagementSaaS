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