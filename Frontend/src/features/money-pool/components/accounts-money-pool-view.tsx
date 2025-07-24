import React from "react";
import { useForm } from "react-hook-form";

import { useMoneyPoolStore } from "features/money-pool/store/money-pool-store";

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
    <form
      onSubmit={handleSubmit(onSubmit)}
      className="max-w-md mx-auto p-6 bg-white rounded-xl border mt-8"
    >
      <h2 className="text-2xl font-bold text-red-500 mb-6">Money pool setup</h2>

      <label className="block mb-2 font-medium">
        Amount collected per person
      </label>
      <input
        type="number"
        {...register("amountCollectedPerPerson", { valueAsNumber: true })}
        className="w-full border rounded px-4 py-2 mb-6 text-lg"
      />

      <label className="block mb-2 font-medium">
        Company contribution multiplier
      </label>
      <select
        {...register("companyContributionMultiplier", { valueAsNumber: true })}
        className="w-full border rounded px-4 py-2 mb-6 text-lg"
      >
        <option value="">Select</option>
        {multipliers.map((mult) => (
          <option key={mult} value={mult}>
            {mult}X
          </option>
        ))}
      </select>

      <button
        type="submit"
        className="w-full bg-yellow-200 border border-yellow-400 text-red-500 font-bold py-2 rounded-lg text-xl mt-4 hover:bg-yellow-300 transition"
      >
        Save
      </button>
    </form>
  );
};

export default AccountsMoneyPoolView;
