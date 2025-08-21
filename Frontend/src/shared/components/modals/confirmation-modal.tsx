import React from "react";

import SpinnerIcon from "assets/components/spinner-icon";
import Modal from "shared/components/modal";

interface ConfirmationModalProps {
  isOpen: boolean;
  title: string;
  description: string;
  successButtonText: string;
  cancelButtonText: string;
  onSuccess: () => void;
  onCancel: () => void;
  isLoading?: boolean;
  variant?: "default" | "danger";
}

const ConfirmationModal: React.FC<ConfirmationModalProps> = ({
  isOpen,
  title,
  description,
  successButtonText,
  cancelButtonText,
  onSuccess,
  onCancel,
  isLoading = false,
  variant = "default",
}) => {
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

  return (
    <Modal
      isOpen={isOpen}
      onClose={onCancel}
      title={title}
      closable={!isLoading}
      closeOnOutsideClick={!isLoading}
    >
      <div className="space-y-6">
        {/* Description */}
        <div className="text-black text-sm leading-relaxed">{description}</div>

        {/* Action Buttons */}
        <div className="flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
          <button
            onClick={onCancel}
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
            onClick={onSuccess}
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
