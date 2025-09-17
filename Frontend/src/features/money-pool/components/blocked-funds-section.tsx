import { useQuery } from "@tanstack/react-query";
import React from "react";

import BlockFundsForm from "features/money-pool/components/block-funds-form";
import BlockedFundsList from "features/money-pool/components/blocked-funds-list";

import { getMoneyPool } from "features/money-pool/api";
import { useBlockedFundsFormStore } from "features/money-pool/store/blocked-funds-store";
import {
  GET_MONEY_POOL_RETRY,
  GET_MONEY_POOL_STALE_TIME,
} from "shared/helpers/constants";

const BlockedFundsSection: React.FC = () => {
  const { isFormOpen, editingFund, openCreateForm } =
    useBlockedFundsFormStore();

  const { data: moneyPoolData } = useQuery({
    queryKey: ["money-pool"],
    queryFn: getMoneyPool,
    staleTime: GET_MONEY_POOL_STALE_TIME,
    retry: GET_MONEY_POOL_RETRY,
  });

  const blockedFunds = moneyPoolData?.blocks || [];
  const totalBlockedAmount = blockedFunds.reduce(
    (sum, fund) => Number(sum) + Number(fund.amount || 0),
    0
  );
  const finalPoolAmount = moneyPoolData?.total_pool_amount || 0;
  const maxAmount = finalPoolAmount - totalBlockedAmount;

  // Show "Add blocked fund" button only when not editing an existing fund
  const showAddButton = !isFormOpen || editingFund !== null;

  return (
    <div>
      <div className="flex items-center justify-between mb-4 mt-7">
        <h3 className="font-extrabold text-xl">Blocked Funds</h3>
        {showAddButton && (
          <button
            onClick={openCreateForm}
            className="inline-flex items-center gap-2 px-3 py-2 rounded-lg border-2 border-black bg-yellow-300 hover:bg-yellow-400 text-black font-extrabold shadow-[2px_2px_0_0_#000] hover:shadow-[3px_3px_0_0_#000] hover:translate-x-[-1px] hover:translate-y-[-1px] transition-all cursor-pointer"
          >
            + Add blocked fund
          </button>
        )}
      </div>

      {/* Show create form only when creating new (not editing existing) */}
      {isFormOpen && !editingFund && (
        <div className="mb-4 p-4 border-2 border-dashed border-black/20 rounded-lg">
          <BlockFundsForm maxAmount={maxAmount} />
        </div>
      )}

      <BlockedFundsList />
    </div>
  );
};

export default BlockedFundsSection;
