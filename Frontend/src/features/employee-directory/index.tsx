import { Edit, Plus, Trash2 } from "lucide-react";
import React, { useState } from "react";

import AddEditEmployeeModal from "features/employee-directory/components/add-edit-employee-modal";
import DataTable, {
  type TableAction,
  type TableColumn,
} from "shared/components/data-table";

export type Employee = {
  id: number;
  name: string;
  email: string;
};

const initialEmployees = [
  { id: 1, name: "Ajai Mathew", email: "ajai@quintet.dev" },
  { id: 2, name: "Rahul R", email: "rahul@quintet.dev" },
  { id: 3, name: "Parvathi", email: "parvathi@quintet.dev" },
  { id: 4, name: "Ramsiya", email: "ramsiya@quintet.dev" },
  { id: 5, name: "Sojo", email: "sojo@quintet.dev" },
];

const EmployeeDirectory: React.FC = () => {
  const [employees, setEmployees] = useState<Employee[]>(initialEmployees);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [modalMode, setModalMode] = useState<"add" | "edit">("add");
  const [selectedEmployee, setSelectedEmployee] = useState<Employee | null>(
    null
  );

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
    setEmployees([...employees, { id: employees.length + 1, name, email }]);
  };

  const handleEdit = (id: number) => {
    const emp = employees.find((e) => e.id === id) || null;
    setSelectedEmployee(emp);
    setModalMode("edit");
    setIsModalOpen(true);
  };

  const handleEditSave = (id: number, name: string, email: string) => {
    setEmployees(
      employees.map((emp) => (emp.id === id ? { ...emp, name, email } : emp))
    );
  };

  const handleDelete = (id: number) => {
    setEmployees(employees.filter((emp) => emp.id !== id));
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

      <DataTable data={employees} columns={columns} actions={actions} />

      <AddEditEmployeeModal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        employee={selectedEmployee}
        mode={modalMode}
        onAdd={handleAdd}
        onSave={handleEditSave}
      />
    </div>
  );
};

export default EmployeeDirectory;
