import { yupResolver } from "@hookform/resolvers/yup";
import React, { useEffect } from "react";
import { FormProvider, useForm } from "react-hook-form";

import InputField from "shared/components/form-components/input-field";
import { MultiSelect } from "shared/components/form-components/multi-select";
import Modal from "shared/components/modal";

import {
  snackFormDefaultValues,
  snackSchema,
} from "features/manage-settings/helpers/form-config";
import type {
  CategoryType,
  SnackType,
} from "features/manage-settings/helpers/manage-setting-types";
import * as yup from "yup";

type SnackFormDataType = yup.InferType<typeof snackSchema>;

type SnackModalProps = {
  isOpen: boolean;
  onClose: () => void;
  onSave: (data: SnackFormDataType) => void;
  editData?: SnackType | null;
  categories: CategoryType[];
};

const SnackModal: React.FC<SnackModalProps> = ({
  isOpen,
  onClose,
  onSave,
  editData,
  categories,
}) => {
  const methods = useForm<SnackFormDataType>({
    defaultValues: snackFormDefaultValues,
    resolver: yupResolver(snackSchema),
    mode: "all",
  });

  const { handleSubmit, reset } = methods;

  useEffect(() => {
    if (editData) {
      reset({
        name: editData.name,
        categoryId: editData.category, // Map category to categoryId
        price: Number(editData.pricePerPiece) || 0, // Convert string to number
        notes: "",
      });
    } else {
      reset(snackFormDefaultValues);
    }
  }, [editData, isOpen, reset]);

  const onSubmit = (data: SnackFormDataType) => {
    onSave(data);
    onClose();
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={editData ? "Edit Snack" : "Add Snack"}
      className="max-w-lg"
    >
      <FormProvider {...methods}>
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
          {/* Snack Name */}
          <InputField
            name="name"
            label="Snack Name"
            placeholder="Enter snack name"
            required
          />

          {/* Category */}
          <MultiSelect
            name="categoryId"
            label="Category"
            options={categories.map((category) => ({
              value: category.id,
              label: category.name,
            }))}
            placeholder="Select category"
            isMulti={false}
            required
            className="w-full"
          />

          {/* Price */}
          <InputField
            name="price"
            label="Price"
            placeholder="Enter price"
            type="number"
            required
          />

          {/* Notes */}
          <InputField
            name="notes"
            label="Notes"
            placeholder="Enter any additional notes"
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

export default SnackModal;
