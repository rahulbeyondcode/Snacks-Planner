import React from "react";

import type { EmployeeContribution } from "features/user-contribution/helpers/user-contribution-type";

type EmployeeContributionRowProps = {
  employee: EmployeeContribution;
  onTogglePaid: (userId: number) => void;
};

const styles = {
  paid: "bg-green-200 border-2 border-black text-black",
  unpaid: "bg-red-200 border-2 border-black text-black",
  pendingPaid:
    "bg-green-300 border-2 border-dashed border-green-600 text-black",
  pendingUnpaid: "bg-red-300 border-2 border-dashed border-red-600 text-black",
  actionPill:
    "px-3 py-1.5 rounded-md font-extrabold text-xs sm:text-sm transition-colors cursor-pointer shadow-[2px_2px_0_0_#000] hover:bg-yellow-200 hover:text-black",
};

const EmployeeContributionRow: React.FC<EmployeeContributionRowProps> = ({
  employee,
  onTogglePaid,
}) => {
  // Determine the display status and styling
  const displayStatus =
    employee.pendingStatus !== undefined
      ? employee.pendingStatus
      : employee.status;
  const isPending = employee.hasUnsavedChanges;

  const getStatusStyle = () => {
    if (isPending) {
      return displayStatus ? styles.pendingPaid : styles.pendingUnpaid;
    }
    return displayStatus ? styles.paid : styles.unpaid;
  };

  const getStatusText = () => {
    const baseText = displayStatus ? "Paid" : "UnPaid";
    return isPending ? `${baseText} (Pending)` : baseText;
  };

  return (
    <div
      className={`flex items-center justify-between bg-white rounded-xl px-3 sm:px-4 py-2 shadow-[4px_4px_0_0_#000] ${
        isPending
          ? "border-2 border-dashed border-yellow-500"
          : "border-2 border-black"
      }`}
    >
      <div className="flex items-center gap-2">
        <span className="font-extrabold text-black text-sm sm:text-base truncate">
          {employee.user_name}
        </span>
        {isPending && (
          <span className="text-xs bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded-full font-medium">
            Changed
          </span>
        )}
      </div>
      <div>
        <button
          className={`${styles.actionPill} ${getStatusStyle()}`}
          onClick={() => onTogglePaid(employee.user_id)}
        >
          {getStatusText()}
        </button>
      </div>
    </div>
  );
};

export default EmployeeContributionRow;
