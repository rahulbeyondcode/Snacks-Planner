import React from "react";

import EmployeeContributionRow from "features/user-contribution/components/employee-contribution-row";
import type { EmployeeContribution } from "features/user-contribution/helpers/user-contribution-type";

type EmployeeContributionListProps = {
  employees: EmployeeContribution[];
  onTogglePaid: (id: number) => void;
};

const EmployeeContributionList: React.FC<EmployeeContributionListProps> = ({
  employees,
  onTogglePaid,
}) => {
  return (
    <div className="flex flex-col gap-3 sm:gap-4">
      {employees.map((emp) => (
        <EmployeeContributionRow
          key={emp.id}
          employee={emp}
          onTogglePaid={onTogglePaid}
        />
      ))}
    </div>
  );
};

export default EmployeeContributionList;
