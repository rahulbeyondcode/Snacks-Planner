import { useQuery, useQueryClient } from "@tanstack/react-query";
import React from "react";

import CrossIcon from "assets/components/cross-icon";
import EditIcon from "assets/components/edit-icon";

import BlockFundsForm from "features/money-pool/components/block-funds-form";
import Tooltip from "shared/components/tooltip";

import { deleteBlockedFund, getMoneyPool } from "features/money-pool/api";
import type { BlockedFundType } from "features/money-pool/helpers/money-pool-types";
import { useBlockedFundsFormStore } from "features/money-pool/store/blocked-funds-store";
import {
  GET_MONEY_POOL_RETRY,
  GET_MONEY_POOL_STALE_TIME,
} from "shared/helpers/constants";

const actionButtonClasses =
  "transition-all duration-200 p-2 hover:translate-x-[-1px] hover:translate-y-[-1px] flex items-center justify-center border-2 rounded-lg cursor-pointer";

const BlockedFundsList: React.FC = () => {
  const queryClient = useQueryClient();
  const { editingFund, openEditForm, closeForm } = useBlockedFundsFormStore();

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

  // Calculate data from the fetched money pool data
  const blockedFunds = moneyPoolData?.blocks || [];
  const totalBlockedAmount = blockedFunds.reduce(
    (sum, fund) => Number(sum) + Number(fund.amount || 0),
    0
  );
  const finalPoolAmount = moneyPoolData?.total_pool_amount || 0;
  const maxAmount = finalPoolAmount - totalBlockedAmount;

  const handleFundClick = (fund: BlockedFundType) => {
    if (editingFund?.block_id === fund.block_id) {
      closeForm();
    } else {
      openEditForm(fund);
    }
  };

  const handleDeleteFund = async (blockId: number) => {
    try {
      await deleteBlockedFund(blockId);
      // Invalidate and refetch the money-pool query
      queryClient.invalidateQueries({ queryKey: ["money-pool"] });
    } catch (error) {
      // TODO: You might want to show a toast notification here
      console.error("Failed to delete blocked fund:", error);
    }
  };

  if (isLoading) {
    return (
      <div className="text-center py-6 text-black/70">
        <p className="text-sm">Loading blocked funds...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="text-center py-6 text-red-600">
        <p className="text-sm">Error loading blocked funds</p>
      </div>
    );
  }

  if (blockedFunds.length === 0) {
    return (
      <div className="text-center py-6 text-black/70">
        <p className="text-sm">No blocked funds yet</p>
      </div>
    );
  }

  return (
    <ul className="space-y-3">
      {blockedFunds.map((fund) => {
        const isEditing = editingFund?.block_id === fund.block_id;

        return (
          <li key={fund.block_id} className="space-y-0">
            {isEditing ? (
              // Show form when editing this item
              <div className="border-2 border-dashed border-black/20 rounded-lg p-4">
                <BlockFundsForm maxAmount={maxAmount} />
              </div>
            ) : (
              // Show normal fund item when not editing
              <div className="group relative rounded-2xl px-4 py-4 border-2 border-black bg-gradient-to-r from-yellow-50 to-yellow-100 hover:from-yellow-100 hover:to-yellow-200 cursor-pointer transition-all duration-200 shadow-[4px_4px_0_0_#000] hover:shadow-[6px_6px_0_0_#000] hover:translate-x-[-2px] hover:translate-y-[-2px]">
                <div className="flex justify-between items-start">
                  <div className="flex-1 min-w-0">
                    <div className="mb-2">
                      <h3 className="text-lg font-extrabold text-black truncate">
                        {fund.reason}
                      </h3>
                    </div>

                    <div className="flex flex-col sm:flex-row sm:items-center gap-2 text-sm">
                      <div className="flex items-center gap-1">
                        <span className="w-2 h-2 bg-green-400 border border-black rounded-full"></span>
                        <span className="font-semibold text-black/80">
                          Blocked on:
                        </span>
                        <span className="font-bold text-black">
                          {new Date(fund.block_date).toLocaleDateString(
                            "en-IN",
                            {
                              day: "2-digit",
                              month: "short",
                              year: "numeric",
                            }
                          )}
                        </span>
                      </div>
                    </div>
                  </div>

                  <div className="flex items-center gap-3 ml-4">
                    <div className="text-right">
                      <div className="text-xs font-semibold text-black/60 mb-2">
                        Amount
                      </div>
                      <div className="text-xl font-extrabold text-black">
                        ₹{Number(fund.amount || 0).toLocaleString("en-IN")}
                      </div>
                    </div>

                    <div className="flex items-center gap-2 ml-4">
                      <Tooltip
                        content="Edit blocked fund"
                        position="top"
                        delayDuration={300}
                      >
                        <button
                          className={`${actionButtonClasses} bg-white hover:bg-yellow-100 text-black border-black shadow-[2px_2px_0_0_#000] hover:shadow-[3px_3px_0_0_#000]`}
                          onClick={(e) => {
                            e.stopPropagation();
                            handleFundClick(fund);
                          }}
                        >
                          <EditIcon />
                        </button>
                      </Tooltip>

                      <Tooltip
                        content="Delete blocked fund"
                        position="top"
                        delayDuration={300}
                      >
                        <button
                          className={`${actionButtonClasses} bg-red-50 hover:bg-red-100 text-red-600 border-red-300 hover:border-red-400 shadow-[2px_2px_0_0_#ef4444] hover:shadow-[3px_3px_0_0_#ef4444]`}
                          onClick={(e) => {
                            e.stopPropagation();
                            handleDeleteFund(fund.block_id);
                          }}
                        >
                          <CrossIcon />
                        </button>
                      </Tooltip>
                    </div>
                  </div>
                </div>
              </div>
            )}
          </li>
        );
      })}
    </ul>
  );
};

export default BlockedFundsList;
