import React from "react";

import type { EmployeeContribution } from "features/user-contribution/helpers/user-contribution-type";

type EmployeeContributionRowProps = {
  employee: EmployeeContribution;
  onTogglePaid: (id: number) => void;
};

const styles = {
  paid: "bg-green-100 border-green-400 text-green-900",
  unpaid: "bg-red-200 border-red-400 text-red-700",
  actionPill:
    "w-22 py-1 rounded-lg font-semibold text-sm transition-colors cursor-pointer hover:bg-orange-200 hover:text-orange-800",
};

const EmployeeContributionRow: React.FC<EmployeeContributionRowProps> = ({
  employee,
  onTogglePaid,
}) => {
  return (
    <div className="flex items-center justify-between border-2 border-orange-300 rounded-xl px-4 py-2">
      <span className="font-medium text-orange-700 text-base w-48">
        {employee.name}
      </span>
      <div>
        <button
          className={`${styles.actionPill} mr-3 ${employee.paid ? styles.paid : styles.unpaid}`}
          onClick={() => onTogglePaid(employee.id)}
        >
          {employee.paid ? "Paid" : "UnPaid"}
        </button>
      </div>
    </div>
  );
};

export default EmployeeContributionRow;
