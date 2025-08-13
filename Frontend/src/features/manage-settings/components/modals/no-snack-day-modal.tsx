import { yupResolver } from "@hookform/resolvers/yup";
import React, { useEffect } from "react";
import { FormProvider, useForm } from "react-hook-form";
import * as yup from "yup";

import InputField from "shared/components/form-components/input-field";
import MultiDatePicker from "shared/components/form-components/multi-date-picker";
import Modal from "shared/components/modal";

import {
  noSnackDayFormDefaultValues,
  noSnackDaySchema,
} from "features/manage-settings/helpers/form-config";
import type { NoSnackDayType } from "features/manage-settings/helpers/manage-setting-types";

type NoSnackDayFormDataType = yup.InferType<typeof noSnackDaySchema>;

type NoSnackDayModalProps = {
  isOpen: boolean;
  onClose: () => void;
  onSave: (data: NoSnackDayFormDataType) => void;
  editData?: NoSnackDayType | null;
};

const NoSnackDayModal: React.FC<NoSnackDayModalProps> = ({
  isOpen,
  onClose,
  onSave,
  editData,
}) => {
  const methods = useForm<NoSnackDayFormDataType>({
    defaultValues: noSnackDayFormDefaultValues,
    resolver: yupResolver(noSnackDaySchema),
    mode: "all",
  });

  const { handleSubmit, reset } = methods;

  useEffect(() => {
    if (editData) {
      reset({
        holidayName: editData.holidayName,
        date:
          typeof editData.date === "string"
            ? new Date(editData.date)
            : editData.date || new Date(),
      });
    } else {
      reset(noSnackDayFormDefaultValues);
    }
  }, [editData, isOpen, reset]);

  const onSubmit = (data: NoSnackDayFormDataType) => {
    onSave(data);
    onClose();
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={editData ? "Edit No Snack Day" : "Add No Snack Day"}
    >
      <FormProvider {...methods}>
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
          {/* Holiday Name */}
          <InputField
            name="holidayName"
            label="Holiday Name"
            placeholder="Enter holiday name"
            required
          />

          {/* Date */}
          <MultiDatePicker
            name="date"
            label="Date"
            placeholder="Select a date"
            multiDatePickMode={false}
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

export default NoSnackDayModal;
