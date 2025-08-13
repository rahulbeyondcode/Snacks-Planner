import React from "react";
import { FormProvider, useForm, useWatch } from "react-hook-form";

import { useMoneyPoolStore } from "features/money-pool/store/money-pool-store";
import InputField from "shared/components/form-components/input-field";
import Button from "shared/components/save-button";

const multipliers = [0, 1, 2, 3, 4];

type FormValues = {
  amountCollectedPerPerson: number;
  companyContributionMultiplier: number;
  totalEmployees?: number;
};

const AccountsMoneyPoolView: React.FC = () => {
  const { pool, setPool } = useMoneyPoolStore();
  const methods = useForm<FormValues>({
    defaultValues: {
      amountCollectedPerPerson: pool.amountCollectedPerPerson,
      companyContributionMultiplier: pool.companyContributionMultiplier,
      totalEmployees: pool.totalEmployees,
    },
  });
  const { register, handleSubmit, control } = methods;

  const watchedAmount = useWatch({ control, name: "amountCollectedPerPerson" });
  const watchedMultiplier = useWatch({
    control,
    name: "companyContributionMultiplier",
  });
  const watchedTotalEmployees = useWatch({ control, name: "totalEmployees" });

  const employeesTotal = Number(
    (watchedTotalEmployees || 0) * (watchedAmount || 0)
  );
  const companyTotal = Number(employeesTotal * (watchedMultiplier || 0));
  const computedFinal = employeesTotal + companyTotal;

  const onSubmit = (data: FormValues) => {
    const amount = Number(data.amountCollectedPerPerson ?? 0);
    const totalEmployees = Number(data.totalEmployees ?? 0);
    const multiplier = Number(data.companyContributionMultiplier ?? 0);

    setPool({
      ...pool,
      amountCollectedPerPerson: amount,
      companyContributionMultiplier: multiplier,
      totalEmployees,
      totalCollectedFromEmployees:
        typeof totalEmployees === "number" && typeof amount === "number"
          ? totalEmployees * amount
          : pool.totalCollectedFromEmployees,
      companyContribution:
        typeof multiplier === "number"
          ? (typeof totalEmployees === "number" && typeof amount === "number"
              ? totalEmployees * amount
              : pool.totalCollectedFromEmployees) * multiplier
          : pool.companyContribution,
      finalPoolAmount: (() => {
        const employeesTotal =
          typeof totalEmployees === "number" && typeof amount === "number"
            ? totalEmployees * amount
            : pool.totalCollectedFromEmployees;
        const companyTotal =
          typeof multiplier === "number"
            ? employeesTotal * multiplier
            : pool.companyContribution;
        return employeesTotal + companyTotal;
      })(),
    });
  };

  return (
    <div className="w-full mx-auto mt-6 px-2 sm:px-4">
      <div className="mb-5 sm:mb-6 flex items-center justify-between">
        <h2 className="text-xl sm:text-2xl font-extrabold text-black">
          Money Pool Setup
        </h2>
        <span className="px-2 py-1 rounded-md bg-yellow-300 text-black border-2 border-black text-[10px] font-bold tracking-wide">
          Accounts
        </span>
      </div>

      <FormProvider {...methods}>
        {/* Summary cards - total first, then two equal cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 mb-6 sm:mb-8">
          {/* Total pool amount (full width) */}
          <div className="bg-white rounded-2xl border-2 border-black p-4 sm:p-5 shadow-[4px_4px_0_0_#000] md:col-span-2">
            <div className="text-sm font-semibold text-black/70">
              Total pool amount
            </div>
            <div className="mt-2 sm:mt-3 flex items-center gap-3 sm:gap-4">
              <span className="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight">
                Rs. {Number(computedFinal).toLocaleString()}
              </span>
            </div>
          </div>

          {/* Employee contributions */}
          <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
            <div className="mb-2 font-extrabold text-black">
              Employee contributions
            </div>
            <div className="grid grid-cols-2 gap-2 text-sm">
              <div className="text-black/70">Per person</div>
              <div className="text-right font-extrabold">
                Rs. {Number(watchedAmount || 0).toLocaleString()}
              </div>
              <div className="text-black/70">Total</div>
              <div className="text-right font-extrabold">
                Rs.{" "}
                {Number(
                  (watchedTotalEmployees || 0) * (watchedAmount || 0)
                ).toLocaleString()}
              </div>
            </div>
          </div>

          {/* Company contribution (bottom-left) */}
          <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
            <div className="mb-2 font-extrabold text-black">
              Company contribution
            </div>
            <div className="grid grid-cols-2 gap-2 text-sm">
              <div className="text-black/70">Multiplier</div>
              <div className="text-right font-extrabold">
                {Number(watchedMultiplier || 0)}X
              </div>
              <div className="text-black/70">Total</div>
              <div className="text-right font-extrabold">
                Rs.{" "}
                {Number(
                  (watchedTotalEmployees || 0) *
                    (watchedAmount || 0) *
                    (watchedMultiplier || 0)
                ).toLocaleString()}
              </div>
            </div>
          </div>
        </div>

        {/* Form below */}
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
              placeholder="e.g. 85"
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
            <Button type="submit">Save</Button>
          </div>
        </form>
      </FormProvider>
    </div>
  );
};

export default AccountsMoneyPoolView;
