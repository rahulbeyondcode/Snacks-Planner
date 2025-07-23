import React from "react";

import BlockFundsForm from "features/money-pool/components/block-funds-form";
import { useMoneyPoolStore } from "features/money-pool/store/money-pool-store";

const SnackManagerMoneyPoolView: React.FC = () => {
  const { pool, blockedFunds } = useMoneyPoolStore();

  return (
    <div className="max-w-xl mx-auto p-6 bg-yellow-50 rounded-xl border border-yellow-200 mt-8">
      <h2 className="text-2xl font-bold text-red-500 mb-4">Money pool</h2>
      <div className="grid grid-cols-2 gap-4 bg-yellow-100 rounded-lg p-4 mb-4">
        <div>
          <div className="text-gray-700">Amount collected per person</div>
          <div className="font-semibold">{pool.amountCollectedPerPerson}</div>
        </div>
        <div>
          <div className="text-gray-700">Total collected from employees</div>
          <div className="font-semibold">
            {pool.totalCollectedFromEmployees}
          </div>
        </div>
        <div>
          <div className="text-gray-700">Company contribution multiplier</div>
          <div className="font-semibold">
            {pool.companyContributionMultiplier}X
          </div>
        </div>
        <div>
          <div className="text-gray-700">Company contribution</div>
          <div className="font-semibold">{pool.companyContribution}</div>
        </div>
        <div>
          <div className="text-gray-700">Paid employees</div>
          <div className="font-semibold">{pool.paidEmployees}</div>
        </div>
        <div className="col-span-2 flex flex-col items-center">
          <span className="text-lg text-gray-700">Final pool amount</span>
          <span className="text-3xl font-bold">{pool.finalPoolAmount}</span>
        </div>
      </div>
      <div className="mt-6">
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
      <div className="mt-4">
        <h3 className="font-semibold mb-2">Blocked Funds</h3>
        <ul>
          {blockedFunds.map((fund) => (
            <li
              key={fund.id}
              className="bg-green-100 rounded px-4 py-2 mb-2 flex justify-between items-center"
            >
              <span>
                {fund.name} ({new Date(fund.date).toLocaleDateString()})
              </span>
              <span className="font-semibold">
                Rs. {fund.amount.toLocaleString()}
              </span>
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
};

export default SnackManagerMoneyPoolView;
