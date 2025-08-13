import React from "react";

import type { EmployeeContribution } from "features/user-contribution/helpers/user-contribution-type";

type EmployeeContributionRowProps = {
  employee: EmployeeContribution;
  onTogglePaid: (id: number) => void;
};

const styles = {
  paid: "bg-green-200 border-2 border-black text-black",
  unpaid: "bg-red-200 border-2 border-black text-black",
  actionPill:
    "px-3 py-1.5 rounded-md font-extrabold text-xs sm:text-sm transition-colors cursor-pointer shadow-[2px_2px_0_0_#000] hover:bg-yellow-200 hover:text-black",
};

const EmployeeContributionRow: React.FC<EmployeeContributionRowProps> = ({
  employee,
  onTogglePaid,
}) => {
  return (
    <div className="flex items-center justify-between bg-white border-2 border-black rounded-xl px-3 sm:px-4 py-2 shadow-[4px_4px_0_0_#000]">
      <span className="font-extrabold text-black text-sm sm:text-base truncate">
        {employee.name}
      </span>
      <div>
        <button
          className={`${styles.actionPill} ${employee.paid ? styles.paid : styles.unpaid}`}
          onClick={() => onTogglePaid(employee.id)}
        >
          {employee.paid ? "Paid" : "UnPaid"}
        </button>
      </div>
    </div>
  );
};

export default EmployeeContributionRow;
