import { create } from "zustand";

import type { BlockedFundType, MoneyPoolType } from "features/money-pool/types";

type MoneyPoolState = {
  pool: MoneyPoolType;
  blockedFunds: BlockedFundType[];
  setPool: (pool: MoneyPoolType) => void;
  addBlockedFund: (fund: BlockedFundType) => void;
};

export const useMoneyPoolStore = create<MoneyPoolState>((set) => ({
  pool: {
    amountCollectedPerPerson: 200,
    companyContributionMultiplier: 2,
    paidEmployees: 70,
    totalCollectedFromEmployees: 14000,
    companyContribution: 28000,
    finalPoolAmount: 42000,
  },
  blockedFunds: [
    {
      id: "1",
      name: "Grill fund",
      date: "2025-07-30",
      amount: "11000",
    },
  ],
  setPool: (pool) => set({ pool }),
  addBlockedFund: (fund) =>
    set((state) => ({ blockedFunds: [...state.blockedFunds, fund] })),
}));
