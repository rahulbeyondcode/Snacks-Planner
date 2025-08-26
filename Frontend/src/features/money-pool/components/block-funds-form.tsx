import { yupResolver } from "@hookform/resolvers/yup";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import React, { useEffect } from "react";
import { FormProvider, useForm } from "react-hook-form";

import MultiDatePicker from "shared/components/form-components/multi-date-picker";
import Button from "shared/components/save-button";

import { createBlockedFund } from "features/money-pool/api";
import {
  blockFundsFormDefaultValues,
  blockFundsFormSchema,
} from "features/money-pool/helpers/form-config";
import type { BlockedFundType } from "features/money-pool/helpers/money-pool-types";

type BlockFundsFormProps = {
  maxAmount: number;
  editingFund?: BlockedFundType | null;
  onCancelEdit?: () => void;
};

type FormValues = {
  name: string;
  date: string;
  amount: string;
};

const BlockFundsForm: React.FC<BlockFundsFormProps> = ({
  maxAmount,
  editingFund,
  onCancelEdit,
}) => {
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
    setValue,
  } = methods;

  const isEditMode = !!editingFund;

  // Effect to populate form when editing
  useEffect(() => {
    if (editingFund) {
      setValue("name", editingFund.reason);
      setValue("date", editingFund.block_date);
      setValue("amount", editingFund.amount.toString());
    } else {
      reset(blockFundsFormDefaultValues);
    }
  }, [editingFund, setValue, reset]);

  const createBlockedFundMutation = useMutation({
    mutationFn: createBlockedFund,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["money-pool"] });
      reset(blockFundsFormDefaultValues);
      if (onCancelEdit) onCancelEdit();
    },
    onError: (error) => {
      // TODO: Handle error if needed
      console.error("Failed to create blocked fund:", error);
    },
  });

  const onSubmit = (data: FormValues) => {
    const payload = {
      money_pool_id: 1, // TODO: get from context
      reason: data.name,
      block_date: data.date,
      amount: Number(data.amount) || 0,
    };
    createBlockedFundMutation.mutate(payload);
  };

  return (
    <FormProvider {...methods}>
      <form
        onSubmit={handleSubmit(onSubmit)}
        className="bg-white border-2 border-black rounded-2xl p-5 sm:p-6 shadow-[6px_6px_0_0_#000]"
      >
        <div className="flex items-center justify-between mb-4">
          <div className="flex items-center gap-3">
            <h4 className="text-lg font-extrabold text-black">
              {isEditMode ? "Edit Blocked Fund" : "Add New Blocked Fund"}
            </h4>
            {isEditMode && (
              <span className="px-2 py-1 rounded-md bg-blue-200 text-xs text-black border-2 border-black font-bold">
                ID: {editingFund?.block_id}
              </span>
            )}
          </div>
          <span className="px-2 py-1 rounded-md bg-yellow-300 text-sm text-black border-2 border-black font-bold">
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

          <MultiDatePicker
            label="Choose a date to block funds"
            name="date"
            placeholder="Select a date"
            multiDatePickMode={false}
          />

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

        <div className="mt-5 flex justify-end gap-3">
          {isEditMode && onCancelEdit && (
            <button
              type="button"
              onClick={onCancelEdit}
              className="px-4 py-2 border-2 border-black rounded-lg bg-gray-100 hover:bg-gray-200 font-semibold text-black transition-colors shadow-[2px_2px_0_0_#000] hover:shadow-[3px_3px_0_0_#000] hover:translate-x-[-1px] hover:translate-y-[-1px]"
            >
              Cancel
            </button>
          )}
          <Button type="submit" disabled={createBlockedFundMutation.isPending}>
            {createBlockedFundMutation.isPending
              ? isEditMode
                ? "Updating..."
                : "Saving..."
              : isEditMode
                ? "Update Fund"
                : "Save Fund"}
          </Button>
        </div>
      </form>
    </FormProvider>
  );
};

export default BlockFundsForm;
