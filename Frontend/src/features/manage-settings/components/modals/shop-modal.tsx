import { yupResolver } from "@hookform/resolvers/yup";
import React, { useEffect } from "react";
import { FormProvider, useForm } from "react-hook-form";

import InputField from "shared/components/form-components/input-field";
import MultiSelect from "shared/components/form-components/multi-select";
import Modal from "shared/components/modal";

import { PAYMENT_MODES as paymentModes } from "features/manage-settings/helpers/constants";
import {
  shopFormDefaultValues,
  shopSchema,
} from "features/manage-settings/helpers/form-config";
import type {
  ShopFormDataType,
  ShopType,
} from "features/manage-settings/helpers/manage-setting-types";

type ShopModalProps = {
  isOpen: boolean;
  onClose: () => void;
  onSave: (data: ShopFormDataType) => void;
  editData?: ShopType | null;
};

const ShopModal: React.FC<ShopModalProps> = ({
  isOpen,
  onClose,
  onSave,
  editData,
}) => {
  const methods = useForm<ShopFormDataType>({
    defaultValues: shopFormDefaultValues,
    resolver: yupResolver(shopSchema),
    mode: "all",
  });

  const { handleSubmit, reset } = methods;

  useEffect(() => {
    if (editData) {
      reset({
        name: editData.name,
        address: editData.address,
        contactDetails: editData.contactDetails,
        paymentMode: editData.paymentMode,
        notes: editData.notes,
      });
    } else {
      reset(shopFormDefaultValues);
    }
  }, [editData, isOpen, reset]);

  const onSubmit = (data: ShopFormDataType) => {
    onSave(data);
    onClose();
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={editData ? "Edit Shop" : "Add Shop"}
      className="max-w-lg"
    >
      <FormProvider {...methods}>
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
          <InputField
            name="name"
            label="Shop Name"
            placeholder="Enter shop name"
            required
          />

          <InputField
            name="address"
            label="Shop Address"
            placeholder="Enter shop address"
            required
          />

          <InputField
            name="contactDetails"
            label="Contact Details"
            placeholder="Enter contact details"
            required
          />

          {/* Payment Mode */}
          <MultiSelect
            name="paymentMode"
            label="Payment Mode"
            options={paymentModes.map((mode) => ({
              value: mode,
              label: mode,
            }))}
            placeholder="Select payment mode"
            isMulti={false}
            required
            className="w-full"
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
              className="px-6 py-2 bg-blue-500 text-white rounded font-handwriting hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              Save
            </button>
          </div>
        </form>
      </FormProvider>
    </Modal>
  );
};

export default ShopModal;
