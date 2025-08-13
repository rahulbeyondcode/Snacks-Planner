import React from "react";

import BlockFundsForm from "features/money-pool/components/block-funds-form";
import { useMoneyPoolStore } from "features/money-pool/store/money-pool-store";

const SnackManagerMoneyPoolView: React.FC = () => {
  const { pool, blockedFunds } = useMoneyPoolStore();

  return (
    <div className="w-full mx-auto mt-6 px-2 sm:px-4">
      <div className="mb-4 sm:mb-6 flex items-center justify-between">
        <h2 className="text-xl sm:text-2xl font-extrabold text-black">
          Money Pool
        </h2>
        <span className="px-2 py-1 rounded-md bg-yellow-300 text-black border-2 border-black text-[10px] font-bold tracking-wide">
          Snack Manager
        </span>
      </div>

      <div className="grid grid-cols-1 gap-3">
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div className="bg-yellow-200 rounded-xl border-2 border-black p-4 shadow-[6px_6px_0_0_#000] flex items-center justify-between">
            <div className="text-black/70 text-sm">Final pool amount</div>
            <div className="text-2xl sm:text-3xl font-extrabold">
              {pool.finalPoolAmount.toLocaleString?.() ?? pool.finalPoolAmount}
            </div>
          </div>
          <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[6px_6px_0_0_#000] flex items-center justify-between">
            <div className="text-black/70 text-sm">Available pool amount</div>
            <div className="text-xl sm:text-2xl font-extrabold">
              {(
                pool.finalPoolAmount -
                blockedFunds.reduce(
                  (sum, fund) => Number(sum) + Number(fund.amount),
                  0
                )
              ).toLocaleString()}
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
            <div className="text-black/70 text-sm">
              Amount collected per person
            </div>
            <div className="font-extrabold text-lg">
              {pool.amountCollectedPerPerson}
            </div>
          </div>
          <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
            <div className="text-black/70 text-sm">
              Total collected from employees
            </div>
            <div className="font-extrabold text-lg">
              {pool.totalCollectedFromEmployees}
            </div>
          </div>
          <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
            <div className="text-black/70 text-sm">
              Company contribution multiplier
            </div>
            <div className="font-extrabold text-lg">
              {pool.companyContributionMultiplier}X
            </div>
          </div>
          <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
            <div className="text-black/70 text-sm">Company contribution</div>
            <div className="font-extrabold text-lg">
              {pool.companyContribution}
            </div>
          </div>
          <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
            <div className="text-black/70 text-sm">Paid employees</div>
            <div className="font-extrabold text-lg">{pool.paidEmployees}</div>
          </div>
        </div>

        <div className="grid grid-cols-1 gap-3">
          <div className="bg-white rounded-2xl border-2 border-black p-4 sm:p-5 shadow-[6px_6px_0_0_#000]">
            <div className="flex items-center justify-between mb-3">
              <h3 className="font-extrabold">Blocked Funds</h3>
            </div>
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
                  maxAmount={
                    pool.finalPoolAmount -
                    blockedFunds.reduce(
                      (sum, fund) => Number(sum) + Number(fund.amount),
                      0
                    )
                  }
                />
              </div>
            </details>
            <ul className="space-y-2">
              {blockedFunds.map((fund) => (
                <li
                  key={fund.id}
                  className="group rounded-lg px-3 py-2 border-2 border-black flex justify-between items-center bg-yellow-50 hover:bg-yellow-200 cursor-pointer transition shadow-[2px_2px_0_0_#000]"
                  title="Click to view/edit"
                >
                  <span className="text-sm sm:text-base font-semibold">
                    {fund.name} (
                    <span className="text-black/70">
                      {new Date(fund.date).toLocaleDateString()}
                    </span>
                    )
                  </span>
                  <div className="flex items-center gap-2">
                    <span className="font-extrabold bg-yellow-300 border-2 border-black rounded-md px-2 py-0.5">
                      Rs. {fund.amount.toLocaleString()}
                    </span>
                    <span className="hidden group-hover:inline-block text-[10px] font-bold bg-black text-yellow-50 border border-black rounded px-2 py-0.5">
                      Edit
                    </span>
                  </div>
                </li>
              ))}
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SnackManagerMoneyPoolView;
