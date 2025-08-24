import { create } from "zustand";

import type { BlockedFundType } from "features/money-pool/helpers/money-pool-types";

type MoneyPoolState = {
  blockedFunds: BlockedFundType[];
  setBlockedFunds: (funds: BlockedFundType[]) => void;
};

export const useMoneyPoolStore = create<MoneyPoolState>((set) => ({
  blockedFunds: [],
  setBlockedFunds: (funds: BlockedFundType[]) => set({ blockedFunds: funds }),
}));
