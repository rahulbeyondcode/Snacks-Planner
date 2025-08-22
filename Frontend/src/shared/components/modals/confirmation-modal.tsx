import React from "react";

import SpinnerIcon from "assets/components/spinner-icon";
import Modal from "shared/components/modal";

import { useModalStore } from "shared/components/modals/store";

const ConfirmationModal: React.FC = () => {
  const { confirmAction, resetModalData } = useModalStore();

  const {
    title = "Confirm Action",
    description = "Are you sure?",
    successButtonText = "Confirm",
    cancelButtonText = "Cancel",
    variant = "default",
    isLoading = false,
    closable = true,
    closeOnOutsideClick = true,
    onSuccess,
    onClose,
  } = confirmAction.extraProps;

  const handleCancel = () => {
    onClose?.();
    resetModalData("confirmAction");
  };

  const handleSuccess = () => {
    onSuccess?.();
  };
  const getButtonStyles = () => {
    const generalStyles =
      "px-4 py-3 rounded-lg border-2 border-black font-semibold text-sm shadow-[4px_4px_0_0_#000] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-black";

    if (variant === "danger") {
      return {
        generalStyles,
        success: "bg-red-500 hover:bg-red-600 text-yellow-50",
        cancel: "bg-yellow-300 hover:bg-yellow-400 text-black",
      };
    }
    return {
      generalStyles,
      success: "bg-black hover:bg-gray-900 text-yellow-50",
      cancel: "bg-yellow-300 hover:bg-yellow-400 text-black",
    };
  };

  const buttonStyles = getButtonStyles();

  if (!confirmAction.isVisible) return null;

  return (
    <Modal
      isOpen
      onClose={handleCancel}
      title={title}
      closable={closable && !isLoading}
      closeOnOutsideClick={closeOnOutsideClick && !isLoading}
    >
      <div className="space-y-6">
        {/* Description */}
        <div className="text-black text-sm leading-relaxed">{description}</div>

        {/* Action Buttons */}
        <div className="flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
          <button
            onClick={handleCancel}
            disabled={isLoading}
            className={`
              ${buttonStyles.generalStyles} 
              ${buttonStyles.cancel}
            `}
            type="button"
          >
            {cancelButtonText || "Cancel"}
          </button>

          <button
            onClick={handleSuccess}
            disabled={isLoading}
            className={`
              ${buttonStyles.generalStyles} 
              ${buttonStyles.success}
              ${isLoading ? "cursor-wait" : ""}
            `}
            type="button"
          >
            {isLoading ? (
              <span className="flex items-center justify-center gap-2">
                <SpinnerIcon />
                Loading...
              </span>
            ) : (
              successButtonText
            )}
          </button>
        </div>
      </div>
    </Modal>
  );
};

export default ConfirmationModal;
