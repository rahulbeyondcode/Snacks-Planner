import { yupResolver } from "@hookform/resolvers/yup";
import React, { useEffect } from "react";
import { FormProvider, useForm } from "react-hook-form";

import InputField from "shared/components/form-components/input-field";
import Modal from "shared/components/modal";

import {
  categoryFormDefaultValues,
  categorySchema,
} from "features/manage-settings/helpers/form-config";
import type {
  CategoryFormDataType,
  CategoryType,
} from "features/manage-settings/helpers/manage-setting-types";

type CategoryModalProps = {
  isOpen: boolean;
  onClose: () => void;
  onSave: (data: CategoryFormDataType) => void;
  editData?: CategoryType | null;
};

const CategoryModal: React.FC<CategoryModalProps> = ({
  isOpen,
  onClose,
  onSave,
  editData,
}) => {
  const methods = useForm<CategoryFormDataType>({
    defaultValues: categoryFormDefaultValues,
    resolver: yupResolver(categorySchema),
    mode: "all",
  });

  const { handleSubmit, reset } = methods;

  useEffect(() => {
    if (editData) {
      reset({
        name: editData.name,
      });
    } else {
      reset(categoryFormDefaultValues);
    }
  }, [editData, isOpen, reset]);

  const onSubmit = (data: CategoryFormDataType) => {
    onSave(data);
    onClose();
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={editData ? "Edit Category" : "Add Category"}
    >
      <FormProvider {...methods}>
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
          {/* Category Name */}
          <InputField
            name="name"
            label="Category Name"
            placeholder="Enter category name"
            required
          />

          {/* Submit Button */}
          <div className="flex justify-end pt-4">
            <button
              type="submit"
              className="inline-flex items-center justify-center px-6 py-2 rounded-lg border-2 border-black bg-yellow-300 text-black font-extrabold shadow-[2px_2px_0_0_#000] hover:bg-yellow-400 focus:outline-none"
            >
              Save
            </button>
          </div>
        </form>
      </FormProvider>
    </Modal>
  );
};

export default CategoryModal;
