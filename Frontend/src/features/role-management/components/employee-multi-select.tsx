import React from "react";

import type { Employee } from "features/role-management/types";
import {
  MultiSelect,
  type OptionType,
} from "shared/components/form-components/multi-select";

type Props = {
  employees: Employee[];
  selected: string[];
  onChange: (ids: string[]) => void;
};

const EmployeeMultiSelect: React.FC<Props> = ({
  employees,
  selected,
  onChange,
}) => {
  const options: OptionType[] = employees.map((employee) => ({
    value: employee.id,
    label: employee.name + (employee.email ? ` (${employee.email})` : ""),
  }));

  return (
    <MultiSelect
      options={options}
      selected={selected}
      onChange={onChange}
      placeholder="Select members..."
    />
  );
};

export default EmployeeMultiSelect;
