export interface ModalProps {
  title: string;
  isVisible: boolean;
  description: string;
  successButtonText: string;
  cancelButtonText: string;
  variant: "default" | "danger";
  isLoading: boolean;
  closable: boolean;
  closeOnOutsideClick: boolean;
  onSuccess: () => void | Promise<void>;
  onClose: () => void;
  modalData: Record<string, any>; // Modal-specific properties as key-value pairs
}

export type ModalState = {
  isVisible: boolean;
  extraProps: Partial<ModalProps>;
};

export type ModalNames = "confirmAction" | "infoModal";

export type ModalStore = {
  [key in ModalNames]: ModalState;
};
