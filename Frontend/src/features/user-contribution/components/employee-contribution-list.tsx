import React, { useMemo } from "react";

import EmployeeContributionRow from "features/user-contribution/components/employee-contribution-row";
import { useUserContributionStore } from "features/user-contribution/store";

const EmployeeContributionList: React.FC = () => {
  const { filter, search, contributionData } = useUserContributionStore();

  const filteredEmployees = useMemo(() => {
    if (!contributionData) return [];

    const allEmployees = Object.values(contributionData.contributions);

    return allEmployees.filter((employee) => {
      const displayStatus =
        employee.pending_status !== undefined
          ? employee.pending_status
          : employee.status;

      if (filter === "paid" && displayStatus !== "paid") return false;
      if (filter === "unpaid" && displayStatus !== "unpaid") return false;
      if (
        search &&
        !employee.user_name.toLowerCase().includes(search.toLowerCase())
      )
        return false;
      return true;
    });
  }, [contributionData, filter, search]);

  return (
    <div className="flex flex-col gap-3 sm:gap-4">
      {filteredEmployees.map((employee) => (
        <EmployeeContributionRow key={employee.user_id} employee={employee} />
      ))}
    </div>
  );
};

export default EmployeeContributionList;
