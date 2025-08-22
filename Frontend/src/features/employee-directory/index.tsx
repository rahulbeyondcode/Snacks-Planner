import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { Edit, Plus, Trash2 } from "lucide-react";
import React from "react";

import AddEditEmployeeModal from "features/employee-directory/components/add-edit-employee-modal";
import DataTable from "shared/components/data-table";

import {
  useEmployeeDirectoryStore,
  type Employee,
} from "features/employee-directory/store";
import type { TableAction, TableColumn } from "shared/components/data-table";

import { deleteEmployee, getEmployees } from "features/employee-directory/api";
import { useModalStore } from "shared/components/modals/store";
import {
  GET_EMPLOYEE_LIST_RETRY,
  GET_EMPLOYEE_LIST_STALE_TIME,
} from "shared/helpers/constants";

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
  const queryClient = useQueryClient();

  const updateModalData = useModalStore((state) => state.updateModalData);
  const resetModalData = useModalStore((state) => state.resetModalData);
  const openCreateModal = useEmployeeDirectoryStore(
    (state) => state.openCreateModal
  );
  const openEditModal = useEmployeeDirectoryStore(
    (state) => state.openEditModal
  );

  const { data: employees, isLoading } = useQuery<Employee[]>({
    queryKey: ["employees"],
    queryFn: getEmployees,
    staleTime: GET_EMPLOYEE_LIST_STALE_TIME,
    retry: GET_EMPLOYEE_LIST_RETRY,
  });

  const deleteEmployeeMutation = useMutation({
    mutationFn: (id: number) => deleteEmployee(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["employees"] });
      resetModalData("confirmAction");
    },
    onError: (error) => {
      console.error("Failed to delete employee:", error);
    },
  });

  // Table action configuration
  const actions: TableAction<Employee>[] = [
    {
      icon: <Edit />,
      onClick: (employee: Employee) => openEditModal(employee),
      className: "hover:text-blue-500",
      title: "Edit Employee",
    },
    {
      icon: <Trash2 />,
      onClick: (employee: Employee) => handleDelete(employee.user_id),
      className: "hover:text-red-500",
      title: "Delete Employee",
    },
  ];

  const handleDelete = (id: number) => {
    const employee = employees?.find((emp) => emp.user_id === id);

    if (employee) {
      updateModalData("confirmAction", {
        isVisible: true,
        extraProps: {
          title: "Delete Employee",
          description: `Are you sure you want to delete "${employee.name}"? This action cannot be undone.`,
          successButtonText: "Delete",
          cancelButtonText: "Cancel",
          variant: "danger",
          onSuccess: () => {
            deleteEmployeeMutation.mutate(id);
          },
        },
      });
    }
  };

  console.log("Rendered");

  return (
    <div className="px-2 py-6 sm:px-4 md:px-6 max-w-7xl w-full mx-auto">
      <div className="flex items-center justify-between mb-4">
        <h2 className="text-xl sm:text-2xl font-extrabold text-black">
          Employee Directory
        </h2>
        <button
          className="inline-flex items-center gap-2 px-3 py-2 rounded-lg border-2 border-black bg-yellow-300 text-black font-extrabold shadow-[2px_2px_0_0_#000] hover:bg-yellow-400 cursor-pointer"
          onClick={openCreateModal}
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

      <AddEditEmployeeModal />
    </div>
  );
};

export default EmployeeDirectory;
