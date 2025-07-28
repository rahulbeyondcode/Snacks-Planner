import React from "react";

type SaveButtonProps = {
  onClick: () => void;
  disabled?: boolean;
};

const SaveButton: React.FC<SaveButtonProps> = ({ onClick, disabled }) => {
  return (
    <button
      className="w-60 mx-auto block py-3 rounded-xl bg-purple-200 text-purple-900 font-bold text-lg shadow-md hover:bg-purple-300 transition-colors border-2 border-purple-400 disabled:opacity-50"
      onClick={onClick}
      disabled={disabled}
    >
      Save
    </button>
  );
};

export default SaveButton;
