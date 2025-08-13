import React from "react";
import { useForm } from "react-hook-form";

import { useMoneyPoolStore } from "features/money-pool/store/money-pool-store";
import Button from "shared/components/save-button";

const multipliers = [0, 1, 2, 3, 4];

type FormValues = {
  amountCollectedPerPerson: number;
  companyContributionMultiplier: number;
};

const AccountsMoneyPoolView: React.FC = () => {
  const { pool, setPool } = useMoneyPoolStore();
  const { register, handleSubmit } = useForm<FormValues>({
    defaultValues: {
      amountCollectedPerPerson: pool.amountCollectedPerPerson,
      companyContributionMultiplier: pool.companyContributionMultiplier,
    },
  });

  const onSubmit = (data: FormValues) => {
    setPool({
      ...pool,
      amountCollectedPerPerson: data.amountCollectedPerPerson,
      companyContributionMultiplier: data.companyContributionMultiplier,
    });
  };

  return (
    <div className="w-full mt-6">
      <div className="max-w-3xl mx-auto px-2 sm:px-4">
        <div className="bg-white rounded-2xl border-2 border-black shadow-[8px_8px_0_0_#000] p-5 sm:p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-xl sm:text-2xl font-extrabold text-black">
              Money Pool Setup
            </h2>
            <span className="px-2 py-1 rounded-md bg-yellow-300 text-black border-2 border-black text-[10px] font-bold tracking-wide">
              Accounts
            </span>
          </div>

          <form
            onSubmit={handleSubmit(onSubmit)}
            className="grid grid-cols-1 sm:grid-cols-2 gap-4"
          >
            <div>
              <label className="block mb-1 text-sm font-semibold text-black">
                Amount collected per person
              </label>
              <div className="flex items-center gap-2">
                <span className="px-3 py-2 rounded-lg border-2 border-black bg-yellow-200 text-black text-sm font-semibold select-none shadow-[2px_2px_0_0_#000]">
                  Rs
                </span>
                <input
                  type="number"
                  {...register("amountCollectedPerPerson", {
                    valueAsNumber: true,
                  })}
                  className="w-full border-2 border-black rounded-lg px-4 py-2.5 text-base focus:outline-none focus:ring-0 bg-white shadow-[2px_2px_0_0_#000]"
                  placeholder="e.g. 500"
                />
              </div>
            </div>

            <div>
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

            <div className="sm:col-span-2 flex justify-end mt-2">
              <Button type="submit">Save</Button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
};

export default AccountsMoneyPoolView;
