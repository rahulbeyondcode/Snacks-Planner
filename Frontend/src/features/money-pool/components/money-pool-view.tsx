import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import React, { useEffect } from "react";
import { FormProvider, useForm, useWatch } from "react-hook-form";

import InputField from "shared/components/form-components/input-field";
import Button from "shared/components/save-button";

import { getMoneyPool, updateMoneyPool } from "features/money-pool/api";
import BlockFundsForm from "features/money-pool/components/block-funds-form";
import type { MoneyPoolFormType } from "features/money-pool/helpers/money-pool-types";
import {
  GET_MONEY_POOL_RETRY,
  GET_MONEY_POOL_STALE_TIME,
} from "shared/helpers/constants";

type MoneyPoolViewProps = {
  userRole: "accounts" | "snack-manager";
};

const multipliers = [0, 1, 2, 3, 4];

const MoneyPoolView: React.FC<MoneyPoolViewProps> = ({ userRole }) => {
  const queryClient = useQueryClient();
  const isAccountsRole = userRole === "accounts";

  const { data: moneyPoolData, isLoading } = useQuery({
    queryKey: ["money-pool"],
    queryFn: getMoneyPool,
    staleTime: GET_MONEY_POOL_STALE_TIME,
    retry: GET_MONEY_POOL_RETRY,
  });

  const methods = useForm<MoneyPoolFormType>({
    defaultValues: {
      amountCollectedPerPerson: 0,
      companyContributionMultiplier: 0,
      totalEmployees: 75, // Default for testing
    },
  });
  const { register, handleSubmit, control, reset } = methods;

  useEffect(() => {
    if (moneyPoolData && moneyPoolData.settings) {
      const formData = {
        amountCollectedPerPerson: moneyPoolData.settings.per_month_amount,
        companyContributionMultiplier: moneyPoolData.settings.multiplier,
        // Calculate total employees from collected amount and per person amount
        totalEmployees:
          moneyPoolData.settings.per_month_amount > 0
            ? Math.round(
                moneyPoolData.total_collected_amount /
                  moneyPoolData.settings.per_month_amount
              )
            : 75, // Default for testing
      };
      reset(formData);
    }
  }, [moneyPoolData, reset]);

  const updateMoneyPoolMutation = useMutation({
    mutationFn: updateMoneyPool,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["money-pool"] });
    },
    onError: (error) => {
      console.error("Failed to update money pool:", error);
    },
  });

  const [watchedAmount, watchedMultiplier, watchedTotalEmployees] = useWatch({
    control,
    name: [
      "amountCollectedPerPerson",
      "companyContributionMultiplier",
      "totalEmployees",
    ],
  });

  // Calculate derived values from API data
  const totalEmployees =
    moneyPoolData && moneyPoolData.settings
      ? moneyPoolData.settings.per_month_amount > 0
        ? Math.round(
            (Number(moneyPoolData.total_collected_amount) || 0) /
              (Number(moneyPoolData.settings.per_month_amount) || 1)
          )
        : 75 // Default for testing
      : 75;

  // Use actual data from API if available, otherwise use form values for real-time calculation
  const displayEmployeesTotal =
    moneyPoolData?.total_collected_amount != null
      ? Number(moneyPoolData.total_collected_amount) || 0
      : Number((watchedTotalEmployees || 75) * (watchedAmount || 0));

  const displayCompanyTotal =
    moneyPoolData?.employer_contribution != null
      ? Number(moneyPoolData.employer_contribution) || 0
      : Number(displayEmployeesTotal * (watchedMultiplier || 0));

  const displayTotalPool =
    moneyPoolData?.total_pool_amount != null
      ? Number(moneyPoolData.total_pool_amount) || 0
      : displayEmployeesTotal + displayCompanyTotal;

  const displayPerPersonAmount =
    moneyPoolData?.settings?.per_month_amount != null
      ? Number(moneyPoolData.settings.per_month_amount) || 0
      : Number(watchedAmount) || 0;

  const displayMultiplier =
    moneyPoolData?.settings?.multiplier != null
      ? Number(moneyPoolData.settings.multiplier) || 0
      : Number(watchedMultiplier) || 0;

  const onSubmit = (data: MoneyPoolFormType) => {
    // Transform form data to API structure in component
    const apiData = {
      per_month_amount: data.amountCollectedPerPerson,
      multiplier: data.companyContributionMultiplier,
    };
    updateMoneyPoolMutation.mutate(apiData);
  };

  if (isLoading) {
    return (
      <div className="w-full mx-auto mt-6 px-2 sm:px-4">
        <div className="mb-5 sm:mb-6 flex items-center justify-between">
          <h2 className="text-xl sm:text-2xl font-extrabold text-black">
            {isAccountsRole ? "Money Pool Setup" : "Money Pool"}
          </h2>
          <span className="px-2 py-1 rounded-md bg-yellow-300 text-black border-2 border-black text-[10px] font-bold tracking-wide">
            {isAccountsRole ? "Accounts" : "Snack Manager"}
          </span>
        </div>
        <div className="flex items-center justify-center py-8">
          <div className="text-lg">Loading money pool data...</div>
        </div>
      </div>
    );
  }

  if (!moneyPoolData) {
    return (
      <div className="w-full mx-auto mt-6 px-2 sm:px-4">
        <div className="mb-5 sm:mb-6 flex items-center justify-between">
          <h2 className="text-xl sm:text-2xl font-extrabold text-black">
            {isAccountsRole ? "Money Pool Setup" : "Money Pool"}
          </h2>
          <span className="px-2 py-1 rounded-md bg-yellow-300 text-black border-2 border-black text-[10px] font-bold tracking-wide">
            {isAccountsRole ? "Accounts" : "Snack Manager"}
          </span>
        </div>
        <div className="flex items-center justify-center py-8">
          <div className="text-lg">No money pool data available</div>
        </div>
      </div>
    );
  }

  return (
    <div className="w-full mx-auto mt-6 px-2 sm:px-4">
      <div className="mb-5 sm:mb-6 flex items-center justify-between">
        <h2 className="text-xl sm:text-2xl font-extrabold text-black">
          {isAccountsRole ? "Money Pool Setup" : "Money Pool"}
        </h2>
        <span className="px-2 py-1 rounded-md bg-yellow-300 text-black border-2 border-black text-[10px] font-bold tracking-wide">
          {isAccountsRole ? "Accounts" : "Snack Manager"}
        </span>
      </div>

      <FormProvider {...methods}>
        <div className="grid grid-cols-1 gap-3">
          {/* Pool amounts box */}
          <div
            className={`bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000] ${
              isAccountsRole ? "md:col-span-2" : ""
            }`}
          >
            <div className="mb-2 font-extrabold text-black">
              {isAccountsRole ? "Total pool amount" : "Pool amounts"}
            </div>
            {isAccountsRole ? (
              <div className="mt-2 sm:mt-3 flex items-center gap-3 sm:gap-4">
                <span className="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight">
                  Rs. {Number(displayTotalPool).toLocaleString()}
                </span>
              </div>
            ) : (
              <div className="space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-black/70 text-sm">Available</span>
                  <span className="inline-flex items-center px-3 py-1 rounded-md bg-yellow-200 border-2 border-black font-extrabold text-lg sm:text-2xl">
                    Rs.{" "}
                    {(
                      Number(moneyPoolData.total_available_amount) || 0
                    ).toLocaleString()}
                  </span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-black/70 text-sm">Total Pool</span>
                  <span className="font-extrabold text-base sm:text-xl">
                    Rs. {Number(displayTotalPool).toLocaleString()}
                  </span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-black/70 text-sm">Blocked</span>
                  <span className="font-extrabold text-red-600 text-base sm:text-xl">
                    Rs.{" "}
                    {(
                      Number(moneyPoolData.blocked_amount) || 0
                    ).toLocaleString()}
                  </span>
                </div>
              </div>
            )}
            {isAccountsRole && moneyPoolData && (
              <div className="mt-4 grid grid-cols-2 gap-4 text-sm">
                <div>
                  <div className="text-black/70">Blocked Amount</div>
                  <div className="font-extrabold text-red-600">
                    Rs.{" "}
                    {(
                      Number(moneyPoolData.blocked_amount) || 0
                    ).toLocaleString()}
                  </div>
                </div>
                <div>
                  <div className="text-black/70">Available Amount</div>
                  <div className="font-extrabold text-green-600">
                    Rs.{" "}
                    {(
                      Number(moneyPoolData.total_available_amount) || 0
                    ).toLocaleString()}
                  </div>
                </div>
              </div>
            )}
          </div>

          {/* Stats boxes */}
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
              <div className="mb-2 font-extrabold text-black">
                Employee contributions
              </div>
              <div className="grid grid-cols-2 gap-2 text-sm">
                <div className="text-black/70">Per person</div>
                <div className="text-right font-extrabold">
                  Rs. {Number(displayPerPersonAmount).toLocaleString()}
                </div>
                <div className="text-black/70">
                  {isAccountsRole ? "Total employees" : "Total"}
                </div>
                <div className="text-right font-extrabold">
                  {isAccountsRole
                    ? totalEmployees
                    : `Rs. ${Number(displayEmployeesTotal).toLocaleString()}`}
                </div>
                {isAccountsRole && (
                  <>
                    <div className="text-black/70">Total</div>
                    <div className="text-right font-extrabold">
                      Rs. {Number(displayEmployeesTotal).toLocaleString()}
                    </div>
                  </>
                )}
              </div>
            </div>

            <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
              <div className="mb-2 font-extrabold text-black">
                Company contribution
              </div>
              <div className="grid grid-cols-2 gap-2 text-sm">
                <div className="text-black/70">Multiplier</div>
                <div className="text-right font-extrabold">
                  {Number(displayMultiplier)}X
                </div>
                <div className="text-black/70">Total</div>
                <div className="text-right font-extrabold">
                  Rs. {Number(displayCompanyTotal).toLocaleString()}
                </div>
              </div>
            </div>

            {!isAccountsRole && (
              <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
                <div className="mb-2 font-extrabold text-black">Employees</div>
                <div className="grid grid-cols-2 gap-2 text-sm">
                  <div className="text-black/70">Total</div>
                  <div className="text-right font-extrabold">
                    {totalEmployees}
                  </div>
                  <div className="text-black/70">Contributing</div>
                  <div className="text-right font-extrabold">
                    {totalEmployees}
                  </div>
                </div>
              </div>
            )}
          </div>

          {/* Form section for accounts role only */}
          {isAccountsRole && (
            <form
              onSubmit={handleSubmit(onSubmit)}
              className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]"
            >
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                <InputField
                  name="amountCollectedPerPerson"
                  label="Amount collected per person"
                  type="number"
                  placeholder="e.g. 500"
                  className="w-full"
                />
                <InputField
                  name="totalEmployees"
                  label="Total employees"
                  type="number"
                  placeholder="e.g. 75"
                  className="w-full"
                  isDisabled
                />
                <div className="sm:col-span-2">
                  <label className="block mb-1 text-sm font-semibold text-black">
                    Company contribution multiplier
                  </label>
                  <div className="relative">
                    <select
                      {...register("companyContributionMultiplier", {
                        valueAsNumber: true,
                      })}
                      className="w-full appearance-none border-2 border-black rounded-lg px-4 py-2.5 text-base bg-white shadow-[2px_2px_0_0_#000]"
                    >
                      <option value="">Select</option>
                      {multipliers.map((mult) => (
                        <option key={mult} value={mult}>
                          {mult}X
                        </option>
                      ))}
                    </select>
                    <div className="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 px-2 py-1 rounded-md bg-black text-yellow-50 text-[10px] font-bold border border-black">
                      ▼
                    </div>
                  </div>
                </div>
              </div>

              <div className="flex justify-end mt-5">
                <Button
                  type="submit"
                  disabled={updateMoneyPoolMutation.isPending || isLoading}
                >
                  {updateMoneyPoolMutation.isPending ? "Saving..." : "Save"}
                </Button>
              </div>
            </form>
          )}

          {/* Blocked Funds Section */}
          <div className="bg-white rounded-2xl border-2 border-black p-4 sm:p-5 shadow-[6px_6px_0_0_#000]">
            <div className="flex items-center justify-between mb-3">
              <h3 className="font-extrabold">Blocked Funds</h3>
            </div>

            {/* Add blocked fund form - only for snack-manager */}
            {!isAccountsRole && (
              <details className="group mb-3">
                <summary className="list-none">
                  <div className="w-full flex justify-end">
                    <span className="inline-flex items-center gap-2 px-3 py-2 rounded-lg border-2 border-black bg-yellow-300 text-black font-extrabold shadow-[2px_2px_0_0_#000]">
                      + Add blocked fund
                    </span>
                  </div>
                </summary>
                <div className="mt-4 pt-4 border-t-2 border-dashed border-black/20">
                  <BlockFundsForm
                    maxAmount={moneyPoolData?.total_available_amount || 0}
                  />
                </div>
              </details>
            )}

            {/* Blocked funds list */}
            <ul className="space-y-2">
              {moneyPoolData.blocks && moneyPoolData.blocks.length > 0 ? (
                moneyPoolData.blocks.map((block) => (
                  <li
                    key={block.block_id}
                    className={`group rounded-lg px-3 py-2 border-2 border-black flex justify-between items-center bg-yellow-50 transition shadow-[2px_2px_0_0_#000] ${
                      !isAccountsRole
                        ? "hover:bg-yellow-200 cursor-pointer"
                        : ""
                    }`}
                    title={!isAccountsRole ? "Click to view/edit" : ""}
                  >
                    <span className="text-sm sm:text-base font-semibold">
                      {block.reason} (
                      <span className="text-black/70">
                        {new Date(block.block_date).toLocaleDateString()}
                      </span>
                      )
                    </span>
                    <div className="flex items-center gap-2">
                      <span className="font-extrabold bg-yellow-300 border-2 border-black rounded-md px-2 py-0.5">
                        Rs. {(Number(block.amount) || 0).toLocaleString()}
                      </span>
                      {!isAccountsRole && (
                        <span className="hidden group-hover:inline-block text-[10px] font-bold bg-black text-yellow-50 border border-black rounded px-2 py-0.5">
                          Edit
                        </span>
                      )}
                    </div>
                  </li>
                ))
              ) : (
                <li className="text-center py-4 text-black/70">
                  No blocked funds
                </li>
              )}
            </ul>
          </div>
        </div>
      </FormProvider>
    </div>
  );
};

export default MoneyPoolView;
