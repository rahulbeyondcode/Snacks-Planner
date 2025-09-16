import { create } from "zustand";

import type { BlockedFundType } from "features/money-pool/helpers/money-pool-types";

type BlockedFundsFormStoreType = {
  isFormOpen: boolean;
  editingFund: BlockedFundType | null;
  openCreateForm: () => void;
  openEditForm: (fund: BlockedFundType) => void;
  closeForm: () => void;
};

export const useBlockedFundsFormStore = create<BlockedFundsFormStoreType>(
  (set) => ({
    isFormOpen: false,
    editingFund: null,

    openCreateForm: () =>
      set({
        isFormOpen: true,
        editingFund: null,
      }),

    openEditForm: (fund: BlockedFundType) =>
      set({
        isFormOpen: true,
        editingFund: fund,
      }),

    closeForm: () =>
      set({
        isFormOpen: false,
        editingFund: null,
      }),
  })
);
