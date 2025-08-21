import { create } from "zustand";

import type { BlockedFundType } from "features/money-pool/helpers/money-pool-types";

// This store is now primarily for blocked funds management
// Money pool data is handled by React Query
type MoneyPoolState = {
  blockedFunds: BlockedFundType[];
  addBlockedFund: (fund: BlockedFundType) => void;
  removeBlockedFund: (id: string) => void;
  setBlockedFunds: (funds: BlockedFundType[]) => void;
};

export const useMoneyPoolStore = create<MoneyPoolState>((set) => ({
  blockedFunds: [],
  addBlockedFund: (fund) =>
    set((state) => ({ blockedFunds: [...state.blockedFunds, fund] })),
  removeBlockedFund: (id) =>
    set((state) => ({
      blockedFunds: state.blockedFunds.filter((fund) => fund.id !== id),
    })),
  setBlockedFunds: (funds) => set({ blockedFunds: funds }),
}));
