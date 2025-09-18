import { useQuery } from "@tanstack/react-query";
import React from "react";

import BlockedFundsSection from "features/money-pool/components/blocked-funds-section";
import InfoCard from "features/money-pool/components/info-card";

import { getMoneyPool } from "features/money-pool/api";
import {
  GET_MONEY_POOL_RETRY,
  GET_MONEY_POOL_STALE_TIME,
} from "shared/helpers/constants";

const SnackManagerMoneyPoolView: React.FC = () => {
  const {
    data: moneyPoolData,
    isLoading,
    error,
  } = useQuery({
    queryKey: ["money-pool"],
    queryFn: getMoneyPool,
    staleTime: GET_MONEY_POOL_STALE_TIME,
    retry: GET_MONEY_POOL_RETRY,
  });

  if (isLoading || error || !moneyPoolData) {
    return (
      <div className="w-full mx-auto mt-6 px-2 sm:px-4">
        <div className="mb-4 sm:mb-6 flex items-center justify-between">
          <h2 className="text-xl sm:text-2xl font-extrabold text-black">
            Money Pool
          </h2>
        </div>
        <div className="flex items-center justify-center py-8">
          {isLoading ? (
            <div className="text-lg">Loading money pool data...</div>
          ) : (
            ""
          )}
          {error ? (
            <div className="text-lg text-red-600">
              Error loading money pool data:{" "}
              {error instanceof Error ? error.message : "Unknown error"}
            </div>
          ) : (
            ""
          )}
          {!moneyPoolData ? (
            <div className="flex items-center justify-center py-8">
              <div className="text-lg">No money pool data available</div>
            </div>
          ) : (
            ""
          )}
        </div>
      </div>
    );
  }

  const blockedFunds = moneyPoolData.blocks || [];
  const totalBlockedAmount = blockedFunds.reduce(
    (sum, fund) => Number(sum) + Number(fund.amount || 0),
    0
  );
  const finalPoolAmount = moneyPoolData.total_pool_amount || 0;
  const availablePoolAmount = finalPoolAmount - totalBlockedAmount;

  console.log("moneyPoolData: ", moneyPoolData);

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
        <div className="bg-white rounded-xl border-2 border-black p-4 shadow-[4px_4px_0_0_#000]">
          <div className="mb-2 font-extrabold text-black">Pool amounts</div>
          <div className="space-y-2">
            <div className="flex items-center justify-between">
              <span className="text-black/70 text-sm">
                Available amount to spend
              </span>
              <span className="inline-flex items-center px-3 py-1 rounded-md bg-yellow-200 border-2 border-black font-extrabold text-lg sm:text-2xl">
                Rs. {Number(availablePoolAmount).toLocaleString()}
              </span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-black/70 text-sm">Total pool amount</span>
              <span className="font-extrabold text-base sm:text-xl">
                Rs. {Number(finalPoolAmount).toLocaleString()}
              </span>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <InfoCard
            title="Employees"
            data={[
              {
                label: "Paid",
                value: moneyPoolData.contribution_counts?.total_paid || 0,
              },
              {
                label: "Unpaid",
                value: moneyPoolData.contribution_counts?.total_unpaid || 0,
              },
            ]}
          />

          <InfoCard
            title="Employee contributions"
            data={[
              {
                label: "Per person",
                value: `Rs. ${Number(
                  moneyPoolData.settings?.per_month_amount || 0
                ).toLocaleString()}`,
              },
              {
                label: "Total",
                value: `Rs. ${Number(
                  moneyPoolData.total_collected_amount || 0
                ).toLocaleString()}`,
              },
            ]}
          />

          <InfoCard
            title="Company contribution"
            data={[
              {
                label: "Multiplier",
                value: `${moneyPoolData.settings?.multiplier || 0}X`,
              },
              {
                label: "Total",
                value: `Rs. ${Number(
                  moneyPoolData.employer_contribution || 0
                ).toLocaleString()}`,
              },
            ]}
          />
        </div>

        <div className="grid grid-cols-1 gap-3">
          <BlockedFundsSection />
        </div>
      </div>
    </div>
  );
};

export default SnackManagerMoneyPoolView;
