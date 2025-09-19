import React from "react";

import { AlertTriangle } from "lucide-react";

type UnsavedChangesAlertProps = {
  isVisible: boolean;
  pendingChangesCount: number;
  onSave: () => void;
  onDiscard: () => void;
  isSaving?: boolean;
};

const UnsavedChangesAlert: React.FC<UnsavedChangesAlertProps> = ({
  isVisible,
  pendingChangesCount,
  onSave,
  onDiscard,
  isSaving = false,
}) => {
  if (!isVisible) return null;

  return (
    <div className="bg-yellow-100 border-2 border-yellow-400 rounded-lg p-4 mb-4 shadow-[2px_2px_0_0_#000]">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <AlertTriangle className="w-5 h-5 text-yellow-600" />
          <div>
            <h4 className="font-bold text-yellow-800 text-sm">
              Unsaved Changes
            </h4>
            <p className="text-yellow-700 text-sm">
              You have {pendingChangesCount} unsaved contribution status{" "}
              {pendingChangesCount === 1 ? "change" : "changes"}.
            </p>
          </div>
        </div>
        <div className="flex gap-2">
          <button
            onClick={onDiscard}
            disabled={isSaving}
            className="px-3 py-1.5 rounded-md border-2 border-black bg-white text-black
              font-semibold text-xs shadow-[2px_2px_0_0_#000] transition-colors
              hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Discard
          </button>
          <button
            onClick={onSave}
            disabled={isSaving}
            className="px-3 py-1.5 rounded-md border-2 border-black bg-black text-white
              font-semibold text-xs shadow-[2px_2px_0_0_#000] transition-colors
              hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {isSaving ? "Saving..." : "Save Changes"}
          </button>
        </div>
      </div>
    </div>
  );
};

export default UnsavedChangesAlert;
