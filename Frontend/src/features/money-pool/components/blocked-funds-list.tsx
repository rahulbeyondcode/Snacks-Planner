import React from "react";

import EditIcon from "assets/components/edit-icon";

import type { BlockedFundType } from "features/money-pool/helpers/money-pool-types";
import { useBlockedFundsFormStore } from "features/money-pool/store/blocked-funds-store";

interface BlockedFundsListProps {
  blockedFunds: BlockedFundType[];
}

const BlockedFundsList: React.FC<BlockedFundsListProps> = ({
  blockedFunds,
}) => {
  const { openEditForm } = useBlockedFundsFormStore();

  const handleFundClick = (fund: BlockedFundType) => {
    openEditForm(fund);
  };

  if (blockedFunds.length === 0) {
    return (
      <div className="text-center py-6 text-black/70">
        <p className="text-sm">No blocked funds yet</p>
      </div>
    );
  }

  return (
    <ul className="space-y-3">
      {blockedFunds.map((fund) => (
        <li
          key={fund.block_id}
          className="group relative rounded-2xl px-4 py-4 border-2 border-black bg-gradient-to-r from-yellow-50 to-yellow-100 hover:from-yellow-100 hover:to-yellow-200 cursor-pointer transition-all duration-200 shadow-[4px_4px_0_0_#000] hover:shadow-[6px_6px_0_0_#000] hover:translate-x-[-2px] hover:translate-y-[-2px]"
          title="Click to edit this blocked fund"
          onClick={() => handleFundClick(fund)}
        >
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
                    {new Date(fund.block_date).toLocaleDateString("en-IN", {
                      day: "2-digit",
                      month: "short",
                      year: "numeric",
                    })}
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

              <button
                className="opacity-0 group-hover:opacity-100 transition-all duration-200 bg-white hover:bg-yellow-100 text-black border-2 border-black rounded-lg px-3 py-2 text-sm font-semibold shadow-[2px_2px_0_0_#000] hover:shadow-[3px_3px_0_0_#000] hover:translate-x-[-1px] hover:translate-y-[-1px] flex items-center gap-1"
                onClick={(e) => {
                  e.stopPropagation();
                  handleFundClick(fund);
                }}
                title="Edit this blocked fund"
              >
                <EditIcon />
                <span>Edit</span>
              </button>
            </div>
          </div>
        </li>
      ))}
    </ul>
  );
};

export default BlockedFundsList;
