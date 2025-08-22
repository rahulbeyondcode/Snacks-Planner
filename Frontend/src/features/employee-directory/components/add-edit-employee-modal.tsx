import { useMutation, useQueryClient } from "@tanstack/react-query";
import React, { useEffect } from "react";
import { useForm } from "react-hook-form";

import { addEmployee, updateEmployee } from "features/employee-directory/api";
import {
  useEmployeeDirectoryStore,
  type Employee,
} from "features/employee-directory/store";
import { toast } from "react-toastify";

type EmployeeFormData = {
  name: string;
  email: string;
};

const AddEditEmployeeModal: React.FC = () => {
  const queryClient = useQueryClient();

  const { isModalOpen, modalMode, selectedEmployee, closeModal } =
    useEmployeeDirectoryStore();

  const isEditMode = modalMode === "edit" && selectedEmployee;

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors, isValid },
  } = useForm<EmployeeFormData>({
    mode: "onChange",
    defaultValues: {
      name: "",
      email: "",
    },
  });

  // Mutations for add and update operations
  const addEmployeeMutation = useMutation({
    mutationFn: (employee: Omit<Employee, "user_id">) => addEmployee(employee),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["employees"] });
      toast.success("Employee added successfully");
      closeModal();
    },
    onError: (error) => {
      toast.error(error.message || "Failed to add employee");
    },
  });

  const updateEmployeeMutation = useMutation({
    mutationFn: (employee: Employee) => updateEmployee(employee),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["employees"] });
      toast.success("Employee updated successfully");
      closeModal();
    },
    onError: (error) => {
      toast.error(error.message || "Failed to update employee");
    },
  });

  const isLoading =
    addEmployeeMutation.isPending || updateEmployeeMutation.isPending;

  useEffect(() => {
    if (isEditMode && selectedEmployee) {
      reset({
        name: selectedEmployee.name || "",
        email: selectedEmployee.email || "",
      });
    } else {
      reset({
        name: "",
        email: "",
      });
    }
  }, [selectedEmployee, isEditMode, isModalOpen, reset]);

  if (!isModalOpen) return null;

  const onSubmit = (data: EmployeeFormData) => {
    if (isEditMode && selectedEmployee) {
      updateEmployeeMutation.mutate({
        user_id: selectedEmployee.user_id,
        name: data.name,
        email: data.email,
      });
    } else {
      addEmployeeMutation.mutate({
        name: data.name,
        email: data.email,
      });
    }
  };

  return (
    <div className="fixed inset-0 flex items-center justify-center bg-black/60 z-50">
      <div className="bg-white rounded-2xl border-2 border-black shadow-[6px_6px_0_0_#000] p-10 w-[92vw] max-w-2xl sm:max-w-3xl max-h-[90vh] overflow-auto">
        <h3 className="text-lg font-extrabold text-black mb-4">
          {isEditMode ? "Edit Employee" : "Add Employee"}
        </h3>
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-3">
          <div>
            <input
              type="text"
              placeholder="Name"
              className={`w-full rounded-md px-3 py-2 border-2 focus:outline-none focus:ring-0 ${
                errors.name ? "border-red-500" : "border-black"
              }`}
              {...register("name", {
                required: "Name is required",
                minLength: {
                  value: 2,
                  message: "Name must be at least 2 characters",
                },
              })}
            />
            {errors.name && (
              <p className="text-red-500 text-sm mt-1">{errors.name.message}</p>
            )}
          </div>
          <div>
            <input
              type="email"
              placeholder="Email"
              className={`w-full rounded-md px-3 py-2 border-2 focus:outline-none focus:ring-0 ${
                errors.email ? "border-red-500" : "border-black"
              }`}
              {...register("email", {
                required: "Email is required",
                pattern: {
                  value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i,
                  message: "Invalid email address",
                },
              })}
            />
            {errors.email && (
              <p className="text-red-500 text-sm mt-1">
                {errors.email.message}
              </p>
            )}
          </div>
          <div className="flex justify-end gap-2 pt-3">
            <button
              type="button"
              onClick={closeModal}
              className="px-4 py-2 rounded-md border-2 border-black bg-white hover:bg-gray-100 shadow-[2px_2px_0_0_#000] cursor-pointer"
            >
              Cancel
            </button>
            <button
              type="submit"
              disabled={isLoading || !isValid}
              className={`px-4 py-2 rounded-md border-2 border-black font-extrabold shadow-[2px_2px_0_0_#000] ${
                isLoading || !isValid
                  ? "bg-gray-300 text-gray-500 cursor-not-allowed"
                  : "bg-yellow-300 text-black hover:bg-yellow-400 cursor-pointer"
              }`}
            >
              {isLoading ? "Saving..." : isEditMode ? "Save" : "Add"}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default AddEditEmployeeModal;
