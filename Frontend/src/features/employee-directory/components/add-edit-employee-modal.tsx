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
              onClick={onClose}
              className="px-4 py-2 rounded-md border-2 border-black bg-white hover:bg-gray-100 shadow-[2px_2px_0_0_#000] cursor-pointer"
            >
              Cancel
            </button>
            <button
              type="submit"
              className="px-4 py-2 rounded-md border-2 border-black bg-yellow-300 text-black font-extrabold hover:bg-yellow-400 shadow-[2px_2px_0_0_#000] cursor-pointer"
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
