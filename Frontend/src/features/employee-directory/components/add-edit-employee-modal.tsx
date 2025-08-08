import React, { useEffect } from "react";
import { useForm } from "react-hook-form";

type EmployeeFormData = {
  name: string;
  email: string;
};

type AddEditEmployeeModalProps = {
  isOpen: boolean;
  onClose: () => void;
  employee?: { id: number; name: string; email: string } | null;
  mode?: "add" | "edit";
  onAdd?: (name: string, email: string) => void;
  onSave?: (id: number, name: string, email: string) => void;
};

const AddEditEmployeeModal: React.FC<AddEditEmployeeModalProps> = ({
  isOpen,
  onClose,
  employee,
  mode = "add",
  onAdd,
  onSave,
}) => {
  const isEditMode = mode === "edit" && employee;

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

  useEffect(() => {
    if (isEditMode) {
      reset({
        name: employee.name || "",
        email: employee.email || "",
      });
    } else {
      reset({
        name: "",
        email: "",
      });
    }
  }, [employee, isEditMode, isOpen, reset]);

  if (!isOpen) return null;

  const onSubmit = (data: EmployeeFormData) => {
    if (isEditMode && onSave && employee) {
      onSave(employee.id, data.name, data.email);
    } else if (!isEditMode && onAdd) {
      onAdd(data.name, data.email);
    }
    onClose();
  };

  return (
    <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50">
      <div className="bg-white rounded-lg shadow-lg p-6 min-w-[300px]">
        <h3 className="text-lg font-bold mb-4">
          {isEditMode ? "Edit Employee" : "Add Employee"}
        </h3>
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-3">
          <div>
            <input
              type="text"
              placeholder="Name"
              className={`w-full border rounded px-3 py-2 ${
                errors.name ? "border-red-500" : ""
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
              className={`w-full border rounded px-3 py-2 ${
                errors.email ? "border-red-500" : ""
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
              onClick={onClose}
              className="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              disabled={!isValid}
              className="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-600 disabled:bg-blue-300 disabled:cursor-not-allowed"
            >
              {isEditMode ? "Save" : "Add"}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default AddEditEmployeeModal;
