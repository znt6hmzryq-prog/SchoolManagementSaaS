"use client";

import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { getClasses, createClass, updateClass, deleteClass } from "@/services/classService";
import { useState, useMemo } from "react";
import { Button } from "@/components/ui/Button";
import { Modal } from "@/components/ui/Modal";
import { Input } from "@/components/ui/Input";
import { Select } from "@/components/ui/Select";
import { Table } from "@/components/ui/Table";
import { Pagination } from "@/components/ui/Pagination";
import { Spinner } from "@/components/ui/Spinner";
import { Toast } from "@/components/ui/Toast";

export default function ClassesPage() {
  const queryClient = useQueryClient();
  const { data: classes, isLoading } = useQuery({
    queryKey: ["classes"],
    queryFn: getClasses,
  });

  const [showModal, setShowModal] = useState(false);
  const [editingClass, setEditingClass] = useState<any>(null);
  const [search, setSearch] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [toast, setToast] = useState<{ message: string; type: 'success' | 'error' } | null>(null);
  const itemsPerPage = 10;

  const [form, setForm] = useState({
    name: "",
    academic_year_id: "",
  });

  const createMutation = useMutation({
    mutationFn: createClass,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["classes"] });
      setToast({ message: "Class created successfully", type: "success" });
      setShowModal(false);
      resetForm();
    },
    onError: () => setToast({ message: "Error creating class", type: "error" }),
  });

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: any }) => updateClass(id, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["classes"] });
      setToast({ message: "Class updated successfully", type: "success" });
      setShowModal(false);
      setEditingClass(null);
      resetForm();
    },
    onError: () => setToast({ message: "Error updating class", type: "error" }),
  });

  const deleteMutation = useMutation({
    mutationFn: deleteClass,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["classes"] });
      setToast({ message: "Class deleted successfully", type: "success" });
    },
    onError: () => setToast({ message: "Error deleting class", type: "error" }),
  });

  const filteredClasses = useMemo(() => {
    if (!classes) return [];
    return classes.filter((cls: any) =>
      cls.name.toLowerCase().includes(search.toLowerCase())
    );
  }, [classes, search]);

  const paginatedClasses = useMemo(() => {
    const start = (currentPage - 1) * itemsPerPage;
    return filteredClasses.slice(start, start + itemsPerPage);
  }, [filteredClasses, currentPage]);

  const totalPages = Math.ceil(filteredClasses.length / itemsPerPage);

  const handleSubmit = (e: any) => {
    e.preventDefault();
    if (editingClass) {
      updateMutation.mutate({ id: editingClass.id, data: form });
    } else {
      createMutation.mutate(form);
    }
  };

  const handleEdit = (cls: any) => {
    setEditingClass(cls);
    setForm({
      name: cls.name,
      academic_year_id: cls.academic_year_id || "",
    });
    setShowModal(true);
  };

  const handleDelete = (id: number) => {
    if (confirm("Are you sure you want to delete this class?")) {
      deleteMutation.mutate(id);
    }
  };

  const resetForm = () => {
    setForm({
      name: "",
      academic_year_id: "",
    });
  };

  const openAddModal = () => {
    setEditingClass(null);
    resetForm();
    setShowModal(true);
  };

  if (isLoading) return <div className="flex justify-center items-center h-64"><Spinner size="lg" /></div>;

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold">Classes</h1>
        <Button onClick={openAddModal}>Add Class</Button>
      </div>

      <div className="mb-4">
        <Input
          type="text"
          placeholder="Search classes..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="max-w-sm"
        />
      </div>

      <Table headers={["ID", "Name", "Academic Year", "Actions"]}>
        {paginatedClasses.map((cls: any) => (
          <tr key={cls.id}>
            <td className="px-6 py-4 whitespace-nowrap">{cls.id}</td>
            <td className="px-6 py-4 whitespace-nowrap">{cls.name}</td>
            <td className="px-6 py-4 whitespace-nowrap">{cls.academicYear?.name || "N/A"}</td>
            <td className="px-6 py-4 whitespace-nowrap space-x-2">
              <Button size="sm" onClick={() => handleEdit(cls)}>Edit</Button>
              <Button size="sm" variant="danger" onClick={() => handleDelete(cls.id)}>Delete</Button>
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
        title={editingClass ? "Edit Class" : "Add Class"}
      >
        <form onSubmit={handleSubmit}>
          <Input
            label="Name"
            value={form.name}
            onChange={(e) => setForm({ ...form, name: e.target.value })}
            required
          />
          <Input
            label="Academic Year ID"
            value={form.academic_year_id}
            onChange={(e) => setForm({ ...form, academic_year_id: e.target.value })}
          />
          <div className="flex justify-end space-x-2 mt-4">
            <Button type="button" variant="secondary" onClick={() => setShowModal(false)}>
              Cancel
            </Button>
            <Button type="submit">{editingClass ? "Update" : "Create"}</Button>
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