import React from "react";
import { HiPencil, HiTrash } from "react-icons/hi";

export type Employee = {
  id: number;
  name: string;
  email: string;
};

interface EmployeeTableProps {
  employees: Employee[];
  onEdit: (id: number) => void;
  onDelete: (id: number) => void;
}

const EmployeeTable: React.FC<EmployeeTableProps> = ({ employees, onEdit, onDelete }) => {
  return (
    <div className="overflow-x-auto rounded-lg border border-red-200">
      <table className="min-w-full bg-white">
        <thead>
          <tr className="bg-red-100">
            <th className="py-2 px-3 text-left font-semibold">Sl No</th>
            <th className="py-2 px-3 text-left font-semibold">Name</th>
            <th className="py-2 px-3 text-left font-semibold">Email</th>
            <th className="py-2 px-3 text-center font-semibold" colSpan={2}></th>
          </tr>
        </thead>
        <tbody>
          {employees.map((emp, idx) => (
            <tr key={emp.id} className="border-b last:border-b-0">
              <td className="py-2 px-3">{idx + 1}</td>
              <td className="py-2 px-3 font-handwriting">{emp.name}</td>
              <td className="py-2 px-3 font-handwriting">{emp.email}</td>
              <td className="py-2 px-3 text-center">
                <button onClick={() => onEdit(emp.id)} className="hover:text-blue-500">
                  <HiPencil />
                </button>
              </td>
              <td className="py-2 px-3 text-center">
                <button onClick={() => onDelete(emp.id)} className="hover:text-red-500">
                  <HiTrash />
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default EmployeeTable;
