import React from "react";

import BlockFundsForm from "features/money-pool/components/block-funds-form";
import { useMoneyPoolStore } from "features/money-pool/store/money-pool-store";

const SnackManagerMoneyPoolView: React.FC = () => {
  const { pool, blockedFunds } = useMoneyPoolStore();
  const totalBlockedAmount = blockedFunds.reduce(
    (sum, fund) => Number(sum) + Number(fund.amount),
    0
  );
  const availablePoolAmount = pool.finalPoolAmount - totalBlockedAmount;

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
        {/* Pool amounts box (single) */}
        <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
          <div className="mb-2 font-extrabold text-black">Pool amounts</div>
          <div className="space-y-2">
            <div className="flex items-center justify-between">
              <span className="text-black/70 text-sm">Available</span>
              <span className="inline-flex items-center px-3 py-1 rounded-md bg-yellow-200 border-2 border-black font-extrabold text-lg sm:text-2xl">
                Rs. {Number(availablePoolAmount).toLocaleString()}
              </span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-black/70 text-sm">Final</span>
              <span className="font-extrabold text-base sm:text-xl">
                Rs. {Number(pool.finalPoolAmount).toLocaleString()}
              </span>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          {/* Box 1: Employee contributions */}
          <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
            <div className="mb-2 font-extrabold text-black">
              Employee contributions
            </div>
            <div className="grid grid-cols-2 gap-2 text-sm">
              <div className="text-black/70">Per person</div>
              <div className="text-right font-extrabold">
                Rs.{" "}
                {Number(pool.amountCollectedPerPerson).toLocaleString?.() ??
                  pool.amountCollectedPerPerson}
              </div>
              <div className="text-black/70">Total</div>
              <div className="text-right font-extrabold">
                Rs.{" "}
                {Number(pool.totalCollectedFromEmployees).toLocaleString?.() ??
                  pool.totalCollectedFromEmployees}
              </div>
            </div>
          </div>

          {/* Box 2: Company contribution */}
          <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
            <div className="mb-2 font-extrabold text-black">
              Company contribution
            </div>
            <div className="grid grid-cols-2 gap-2 text-sm">
              <div className="text-black/70">Multiplier</div>
              <div className="text-right font-extrabold">
                {pool.companyContributionMultiplier}X
              </div>
              <div className="text-black/70">Total</div>
              <div className="text-right font-extrabold">
                Rs.{" "}
                {Number(pool.companyContribution).toLocaleString?.() ??
                  pool.companyContribution}
              </div>
            </div>
          </div>

          {/* Box 3: Employees */}
          <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
            <div className="mb-2 font-extrabold text-black">Employees</div>
            <div className="grid grid-cols-2 gap-2 text-sm">
              <div className="text-black/70">Paid</div>
              <div className="text-right font-extrabold">
                {pool.paidEmployees}
              </div>
              <div className="text-black/70">Unpaid</div>
              <div className="text-right font-extrabold">
                {typeof pool.totalEmployees === "number"
                  ? Math.max(0, pool.totalEmployees - pool.paidEmployees)
                  : "-"}
              </div>
            </div>
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
