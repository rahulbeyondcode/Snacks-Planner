import CrossIcon from "assets/components/cross-icon";
import React, { useEffect } from "react";

type ModalProps = {
  children: React.ReactNode;
  className?: string;
  isOpen: boolean;
  title?: string;
  onClose: () => void;
  closable?: boolean;
  closeOnOutsideClick?: boolean;
};

const Modal: React.FC<ModalProps> = ({
  children,
  className = "",
  isOpen,
  onClose,
  title,
  closable = true,
  closeOnOutsideClick = true,
}) => {
  useEffect(() => {
    const handleEscKey = (event: KeyboardEvent) => {
      if (event.key === "Escape" && closable && isOpen) {
        onClose();
      }
    };

    if (isOpen) {
      document.addEventListener("keydown", handleEscKey);
      return () => document.removeEventListener("keydown", handleEscKey);
    }
  }, [isOpen, closable, onClose]);

  if (!isOpen) return null;

  const handleBackdropClick = (e: React.MouseEvent) => {
    if (e.target === e.currentTarget && closable && closeOnOutsideClick) {
      onClose();
    }
  };

  return (
    <div
      className="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50"
      onClick={handleBackdropClick}
    >
      <div
        className={`bg-white rounded-lg shadow-lg max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto ${className}`}
      >
        {/* Header - Only show if title exists or modal is closable */}
        {(title || closable) && (
          <div className="flex items-center justify-between p-4 border-b">
            {title && (
              <h2 className="text-lg font-handwriting text-gray-800">
                {title}
              </h2>
            )}

            {/* Spacer when no title but closable */}
            {!title && closable && <div />}

            {closable && (
              <button
                onClick={onClose}
                className="p-1 hover:bg-gray-100 rounded"
                type="button"
              >
                <CrossIcon />
              </button>
            )}
          </div>
        )}

        {/* Content */}
        <div className="p-4">{children}</div>
      </div>
    </div>
  );
};

export default Modal;
