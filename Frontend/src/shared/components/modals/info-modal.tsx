import React from "react";

import Modal from "shared/components/modal";
import { useModalStore } from "shared/components/modals/store";

const InfoModal: React.FC = () => {
  const { infoModal, resetModalData } = useModalStore();

  const {
    title = "Information",
    description = "",
    successButtonText = "OK",
    isLoading = false,
    closable = true,
    closeOnOutsideClick = true,
    onClose,
  } = infoModal.extraProps;

  const handleClose = () => {
    onClose?.();
    resetModalData("infoModal");
  };

  if (!infoModal.isVisible) return null;

  return (
    <Modal
      isOpen
      onClose={handleClose}
      title={title}
      closable={closable && !isLoading}
      closeOnOutsideClick={closeOnOutsideClick && !isLoading}
    >
      <div className="space-y-4">
        <div className="text-black text-sm leading-relaxed">
          {description || ""}
        </div>
        <div className="flex justify-end">
          <button
            onClick={handleClose}
            className="px-4 py-3 rounded-lg border-2 border-black font-semibold text-sm
              shadow-[4px_4px_0_0_#000] transition-all duration-200
              bg-black text-yellow-50 hover:bg-gray-900
              focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-black"
            type="button"
          >
            {successButtonText}
          </button>
        </div>
      </div>
    </Modal>
  );
};

export default InfoModal;
