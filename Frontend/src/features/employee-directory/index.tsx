import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { Edit, Plus, Trash2 } from "lucide-react";
import React, { useState } from "react";

import AddEditEmployeeModal from "features/employee-directory/components/add-edit-employee-modal";
import DataTable from "shared/components/data-table";

import {
  addEmployee,
  deleteEmployee,
  getEmployees,
  updateEmployee,
} from "features/employee-directory/api";
import {
  type TableAction,
  type TableColumn,
} from "shared/components/data-table";

export type Employee = {
  id: number;
  name: string;
  email: string;
};

// Table column configuration
const columns: TableColumn<Employee>[] = [
  {
    key: "serialNumber",
    title: "Sl No",
    render: (_value, _item, index) => index + 1,
  },
  {
    key: "name",
    title: "Name",
  },
  {
    key: "email",
    title: "Email",
  },
];

const EmployeeDirectory: React.FC = () => {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [modalMode, setModalMode] = useState<"add" | "edit">("add");
  const [selectedEmployee, setSelectedEmployee] = useState<Employee | null>(
    null
  );
  const queryClient = useQueryClient();

  const { data: employees, isLoading } = useQuery({
    queryKey: ["employees"],
    queryFn: getEmployees,
    staleTime: 1000 * 60 * 5,
    retry: 3,
  });

  const addEmployeeMutation = useMutation({
    mutationFn: (employee: Omit<Employee, "id">) => addEmployee(employee),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["employees"] });
      setIsModalOpen(false);
    },
    onError: (error) => {
      console.error("Failed to add employee:", error);
    },
  });

  const updateEmployeeMutation = useMutation({
    mutationFn: (employee: Employee) => updateEmployee(employee),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["employees"] });
      setIsModalOpen(false);
    },
    onError: (error) => {
      console.error("Failed to update employee:", error);
    },
  });

  const deleteEmployeeMutation = useMutation({
    mutationFn: (id: number) => deleteEmployee(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["employees"] });
    },
    onError: (error) => {
      console.error("Failed to delete employee:", error);
    },
  });

  // Table action configuration
  const actions: TableAction<Employee>[] = [
    {
      icon: <Edit />,
      onClick: (employee: Employee) => handleEdit(employee.id),
      className: "hover:text-blue-500",
      title: "Edit Employee",
    },
    {
      icon: <Trash2 />,
      onClick: (employee: Employee) => handleDelete(employee.id),
      className: "hover:text-red-500",
      title: "Delete Employee",
    },
  ];

  const handleAdd = (name: string, email: string) => {
    addEmployeeMutation.mutate({ name, email });
  };

  const handleEdit = (id: number) => {
    const employeeToEdit =
      employees?.find((employee: Employee) => employee.id === id) || null;
    setSelectedEmployee(employeeToEdit);
    setModalMode("edit");
    setIsModalOpen(true);
  };

  const handleEditSave = (id: number, name: string, email: string) => {
    updateEmployeeMutation.mutate({ id, name, email });
  };

  const handleDelete = (id: number) => {
    if (confirm("Are you sure you want to delete this employee?")) {
      deleteEmployeeMutation.mutate(id);
    }
  };

  return (
    <div className="px-2 py-6 sm:px-4 md:px-6 max-w-7xl w-full mx-auto">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-xl sm:text-2xl font-extrabold text-black">
          Employee Directory
        </h2>
        <button
          className="inline-flex items-center gap-2 px-3 py-2 rounded-lg border-2 border-black bg-yellow-300 text-black font-extrabold shadow-[2px_2px_0_0_#000] hover:bg-yellow-400 cursor-pointer"
          onClick={() => {
            setModalMode("add");
            setSelectedEmployee(null);
            setIsModalOpen(true);
          }}
        >
          <Plus /> Add Employee
        </button>
      </div>

      <DataTable
        data={employees ?? []}
        columns={columns}
        actions={actions}
        isLoading={isLoading}
        skeletonRowCount={15}
      />

      <AddEditEmployeeModal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        employee={selectedEmployee}
        mode={modalMode}
        onAdd={handleAdd}
        onSave={handleEditSave}
        isLoading={
          addEmployeeMutation.isPending || updateEmployeeMutation.isPending
        }
      />
    </div>
  );
};

export default EmployeeDirectory;
