import React from "react";

type SaveButtonProps = {
  onClick: () => void;
  disabled?: boolean;
};

const SaveButton: React.FC<SaveButtonProps> = ({ onClick, disabled }) => (
  <button
    type="button"
    className="bg-green-200 text-black rounded px-8 py-2 mt-4 font-semibold border border-black hover:bg-green-300 disabled:opacity-50"
    onClick={onClick}
    disabled={disabled}
  >
    Save
  </button>
);

export default SaveButton;
