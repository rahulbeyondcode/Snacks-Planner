import { yupResolver } from "@hookform/resolvers/yup";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import React from "react";
import { FormProvider, useForm } from "react-hook-form";

import MultiDatePicker from "shared/components/form-components/multi-date-picker";
import Button from "shared/components/save-button";

import { createBlockedFund } from "features/money-pool/api";
import {
  blockFundsFormDefaultValues,
  blockFundsFormSchema,
} from "features/money-pool/components/form-config";

type BlockFundsFormProps = {
  maxAmount: number;
};

type FormValues = {
  name: string;
  date: string;
  amount: string;
};

const BlockFundsForm: React.FC<BlockFundsFormProps> = ({ maxAmount }) => {
  const queryClient = useQueryClient();

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

  const createBlockedFundMutation = useMutation({
    mutationFn: createBlockedFund,
    onSuccess: () => {
      // Invalidate and refetch money pool data to get updated blocked funds list
      queryClient.invalidateQueries({ queryKey: ["money-pool"] });
      reset();
    },
    onError: (error) => {
      console.error("Failed to create blocked fund:", error);
      // Could add toast notification here
    },
  });

  const onSubmit = (data: FormValues) => {
    // Transform form data to API structure in component
    const apiData = {
      reason: data.name,
      block_date: data.date,
      amount: Number(data.amount) || 0,
    };
    createBlockedFundMutation.mutate(apiData);
  };

  return (
    <FormProvider {...methods}>
      <form
        onSubmit={handleSubmit(onSubmit)}
        className="bg-white border-2 border-black rounded-2xl p-5 sm:p-6 shadow-[6px_6px_0_0_#000]"
      >
        <div className="flex items-center justify-between mb-4">
          <h4 className="text-lg font-extrabold text-black">
            Add New Blocked Fund
          </h4>
          <span className="px-2 py-1 rounded-md bg-yellow-300 text-black border-2 border-black text-[10px] font-bold tracking-wide">
            Max Rs. {Number(maxAmount || 0).toLocaleString()}
          </span>
        </div>

        <div className="grid grid-cols-1 gap-4">
          <div>
            <label className="block mb-1.5 text-sm font-semibold text-black">
              Enter a name for this fund
            </label>
            <input
              type="text"
              {...register("name", { required: "Name is required" })}
              className="w-full border-2 border-black rounded-lg px-3 py-2.5 bg-white shadow-[2px_2px_0_0_#000]"
              placeholder="e.g. Onam Sadhya fund"
            />
            {errors.name && (
              <p className="text-red-600 text-xs mt-1">
                {errors.name.message as string}
              </p>
            )}
          </div>

          <div className="bg-yellow-50 border-2 border-black rounded-lg p-3 shadow-[2px_2px_0_0_#000]">
            <MultiDatePicker
              label="Choose a date to block funds"
              name="date"
              placeholder="Select a date"
              multiDatePickMode={false}
            />
          </div>

          <div>
            <label className="block mb-1.5 text-sm font-semibold text-black">
              Enter the amount to block
            </label>
            <div className="flex items-center gap-2">
              <span className="px-3 py-2 rounded-lg border-2 border-black bg-yellow-200 text-black text-sm font-semibold select-none shadow-[2px_2px_0_0_#000]">
                Rs
              </span>
              <input
                type="number"
                {...register("amount")}
                className="w-full border-2 border-black rounded-lg px-3 py-2.5 bg-white shadow-[2px_2px_0_0_#000]"
                placeholder="e.g. 12,000"
              />
            </div>
            {errors.amount && (
              <p className="text-red-600 text-xs mt-1">
                {errors.amount.message as string}
              </p>
            )}
          </div>
        </div>

        <div className="mt-5 flex justify-end">
          <Button type="submit" disabled={createBlockedFundMutation.isPending}>
            {createBlockedFundMutation.isPending ? "Saving..." : "Save"}
          </Button>
        </div>
      </form>
    </FormProvider>
  );
};

export default BlockFundsForm;
