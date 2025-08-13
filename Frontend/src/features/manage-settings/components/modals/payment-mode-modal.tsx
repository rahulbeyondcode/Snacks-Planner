import { yupResolver } from "@hookform/resolvers/yup";
import React, { useEffect } from "react";
import { FormProvider, useForm } from "react-hook-form";

import InputField from "shared/components/form-components/input-field";
import Modal from "shared/components/modal";

import {
  paymentModeFormDefaultValues,
  paymentModeSchema,
} from "features/manage-settings/helpers/form-config";
import type {
  PaymentModeFormDataType,
  PaymentModeType,
} from "features/manage-settings/helpers/manage-setting-types";

type PaymentModeModalProps = {
  isOpen: boolean;
  onClose: () => void;
  onSave: (data: PaymentModeFormDataType) => void;
  editData?: PaymentModeType | null;
};

const PaymentModeModal: React.FC<PaymentModeModalProps> = ({
  isOpen,
  onClose,
  onSave,
  editData,
}) => {
  const methods = useForm<PaymentModeFormDataType>({
    defaultValues: paymentModeFormDefaultValues,
    resolver: yupResolver(paymentModeSchema),
    mode: "all",
  });

  const { handleSubmit, reset } = methods;

  useEffect(() => {
    if (editData) {
      reset({
        name: editData.name,
      });
    } else {
      reset(paymentModeFormDefaultValues);
    }
  }, [editData, isOpen, reset]);

  const onSubmit = (data: PaymentModeFormDataType) => {
    onSave(data);
    onClose();
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={editData ? "Edit Payment Mode" : "Add Payment Mode"}
    >
      <FormProvider {...methods}>
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
          <InputField
            name="name"
            label="Payment Mode Name"
            placeholder="Enter payment mode name"
            required
          />

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

export default PaymentModeModal;
