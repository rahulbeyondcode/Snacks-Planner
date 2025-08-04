import React, { useState } from "react";
import { HiPlus } from "react-icons/hi";

import AddEmployeeModal from "features/employee-directory/components/add-employee-modal";
import EditEmployeeModal from "features/employee-directory/components/edit-employee-modal";
import EmployeeTable, {
  type Employee,
} from "features/employee-directory/components/employee-table";

const initialEmployees = [
  { id: 1, name: "Ajai Mathew", email: "ajai@quintet.dev" },
  { id: 2, name: "Rahul R", email: "rahul@quintet.dev" },
  { id: 3, name: "Parvathi", email: "parvathi@quintet.dev" },
  { id: 4, name: "Ramsiya", email: "ramsiya@quintet.dev" },
  { id: 5, name: "Sojo", email: "sojo@quintet.dev" },
];

const EmployeeDirectory: React.FC = () => {
  const [employees, setEmployees] = useState<Employee[]>(initialEmployees);
  const [showModal, setShowModal] = useState(false);
  const [editModalOpen, setEditModalOpen] = useState(false);
  const [selectedEmployee, setSelectedEmployee] = useState<Employee | null>(
    null
  );

  const handleAdd = (name: string, email: string) => {
    setEmployees([...employees, { id: employees.length + 1, name, email }]);
  };

  const handleEdit = (id: number) => {
    const emp = employees.find((e) => e.id === id) || null;
    setSelectedEmployee(emp);
    setEditModalOpen(true);
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
    <div className="px-2 py-6 md:px-6 max-w-3xl mx-auto">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-xl font-handwriting font-bold text-red-500">
          Employee Directory
        </h2>
        <button
          className="flex items-center py-2 px-4 rounded bg-blue-200 border border-blue-400 text-blue-900 font-semibold hover:bg-blue-300 cursor-pointer"
          onClick={() => setShowModal(true)}
        >
          <HiPlus /> Add Employee
        </button>
      </div>
      <EmployeeTable
        employees={employees}
        onEdit={handleEdit}
        onDelete={handleDelete}
      />
      <AddEmployeeModal
        isOpen={showModal}
        onClose={() => setShowModal(false)}
        onAdd={handleAdd}
      />
      <EditEmployeeModal
        isOpen={editModalOpen}
        onClose={() => setEditModalOpen(false)}
        employee={selectedEmployee}
        onSave={handleEditSave}
      />
    </div>
  );
};

export default EmployeeDirectory;
