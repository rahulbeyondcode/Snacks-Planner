import { yupResolver } from "@hookform/resolvers/yup";
import React from "react";
import { FormProvider, useForm } from "react-hook-form";

import MultiDatePicker from "shared/components/form-components/multi-date-picker";
import Button from "shared/components/save-button";

import {
  blockFundsFormDefaultValues,
  blockFundsFormSchema,
} from "features/money-pool/components/form-config";
import { useMoneyPoolStore } from "features/money-pool/store/money-pool-store";

type BlockFundsFormProps = {
  maxAmount: number;
};

type FormValues = {
  name: string;
  date: string;
  amount: string;
};

const BlockFundsForm: React.FC<BlockFundsFormProps> = ({ maxAmount }) => {
  const { addBlockedFund } = useMoneyPoolStore();

  const methods = useForm<FormValues>({
    defaultValues: blockFundsFormDefaultValues,
    context: {
      availablePoolAmount: maxAmount,
    },
    mode: "all",
    resolver: yupResolver(blockFundsFormSchema),
  });

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
  } = methods;

  const onSubmit = (data: FormValues) => {
    addBlockedFund({
      id: Date.now().toString(),
      name: data.name,
      date: data.date,
      amount: data.amount,
    });
    reset();
  };

  return (
    <FormProvider {...methods}>
      <form
        onSubmit={handleSubmit(onSubmit)}
        className="bg-white border rounded-xl p-4 mt-6"
      >
        <h4 className="font-bold mb-2">Add New Blocked Fund</h4>

        <label className="block mb-1">Enter a name for this fund</label>
        <input
          type="text"
          {...register("name", { required: "Name is required" })}
          className="w-full border rounded px-3 py-2 mb-2"
          placeholder="e.g. Onam Sadhya fund"
        />
        {errors.name && (
          <p className="text-red-500 text-xs mb-2">
            {errors.name.message as string}
          </p>
        )}

        <MultiDatePicker
          label="Choose a date to block funds"
          name="date"
          placeholder="Select a date"
          multiDatePickMode={false}
        />

        <label className="block mb-1">
          Enter the amount to block{" "}
          <span className="text-red-500 text-xs">
            (Maximum available Rs. {maxAmount.toLocaleString()})
          </span>
        </label>
        <input
          type="number"
          {...register("amount")}
          className="w-full border rounded px-3 py-2 mb-2"
          placeholder="e.g. 12,000"
        />
        {errors.amount && (
          <p className="text-red-500 text-xs mb-2">
            {errors.amount.message as string}
          </p>
        )}

        <Button type="submit">Save</Button>
      </form>
    </FormProvider>
  );
};

export default BlockFundsForm;
