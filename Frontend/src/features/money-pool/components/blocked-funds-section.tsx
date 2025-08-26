import React, { useState } from "react";

import BlockFundsForm from "features/money-pool/components/block-funds-form";
import BlockedFundsList from "features/money-pool/components/blocked-funds-list";
import type { BlockedFundType } from "features/money-pool/helpers/money-pool-types";

interface BlockedFundsSectionProps {
  blockedFunds: BlockedFundType[];
  maxAmount: number;
}

const BlockedFundsSection: React.FC<BlockedFundsSectionProps> = ({
  blockedFunds,
  maxAmount,
}) => {
  const [editingFund, setEditingFund] = useState<BlockedFundType | null>(null);
  const [isFormOpen, setIsFormOpen] = useState(false);

  const handleEditFund = (fund: BlockedFundType) => {
    setEditingFund(fund);
    setIsFormOpen(true);
  };

  const handleCancelEdit = () => {
    setEditingFund(null);
    setIsFormOpen(false);
  };

  const handleAddNew = () => {
    setEditingFund(null);
    setIsFormOpen(true);
  };
  return (
    <div className="bg-white rounded-2xl border-2 border-black p-4 sm:p-5 shadow-[6px_6px_0_0_#000]">
      <div className="flex items-center justify-between mb-3">
        <h3 className="font-extrabold">Blocked Funds</h3>
      </div>

      <div className="mb-3">
        {!isFormOpen ? (
          <div className="w-full flex justify-end">
            <button
              onClick={handleAddNew}
              className="inline-flex items-center gap-2 px-3 py-2 rounded-lg border-2 border-black bg-yellow-300 hover:bg-yellow-400 text-black font-extrabold shadow-[2px_2px_0_0_#000] hover:shadow-[3px_3px_0_0_#000] hover:translate-x-[-1px] hover:translate-y-[-1px] transition-all"
            >
              + Add blocked fund
            </button>
          </div>
        ) : (
          <div className="mt-4 pt-4 border-t-2 border-dashed border-black/20">
            <BlockFundsForm
              maxAmount={maxAmount}
              editingFund={editingFund}
              onCancelEdit={handleCancelEdit}
            />
          </div>
        )}
      </div>

      <BlockedFundsList
        blockedFunds={blockedFunds}
        onEditFund={handleEditFund}
      />
    </div>
  );
};

export default BlockedFundsSection;
