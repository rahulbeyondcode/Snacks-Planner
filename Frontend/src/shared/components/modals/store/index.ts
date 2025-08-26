import { create } from "zustand";

import type {
  ModalNames,
  ModalProps,
  ModalStore,
} from "shared/components/modals/store/types";

type ModalActions = {
  updateModalData: (
    modalType: ModalNames,
    payload: {
      isVisible: boolean;
      extraProps: Partial<ModalProps>;
    }
  ) => void;
  resetModalData: (modalType: ModalNames) => void;
  resetAllModals: () => void;
};

const initialState: ModalStore = {
  confirmAction: { isVisible: false, extraProps: {} },
  infoModal: { isVisible: false, extraProps: {} },
};

export const useModalStore = create<ModalStore & ModalActions>((set) => ({
  ...initialState,

  updateModalData: (modalType, payload) => {
    const { isVisible, extraProps } = payload;

    set((state) => ({
      ...state,
      [modalType]: {
        ...state[modalType],
        ...(isVisible ? { isVisible } : {}),
        ...(extraProps ? { extraProps } : {}),
      },
    }));
  },

  resetModalData: (modalType) => {
    set((state) => ({
      ...state,
      [modalType]: { isVisible: false, extraProps: {} },
    }));
  },

  resetAllModals: () => {
    set(initialState);
  },
}));
