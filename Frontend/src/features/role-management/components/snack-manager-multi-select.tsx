import React from "react";

import { MultiSelect } from "shared/components/form-components/multi-select";

import type { Employee } from "features/role-management/types";
import type { OptionType } from "shared/components/form-components/multi-select";

type Props = {
  employees: Employee[];
  selected: string[];
  onChange: (ids: string[]) => void;
};

const SnackManagerMultiSelect: React.FC<Props> = ({
  employees,
  selected,
  onChange,
}) => {
  const options: OptionType[] = employees.map((emp) => ({
    value: emp.id,
    label: emp.name + (emp.email ? ` (${emp.email})` : ""),
    isDisabled: selected.length >= 4 && !selected.includes(emp.id),
  }));

  const isDisabled = employees.length === 0;

  return (
    <MultiSelect
      options={options}
      selected={selected}
      onChange={onChange}
      placeholder={
        isDisabled ? "Select members first" : "Select snack managers..."
      }
      isDisabled={isDisabled}
      isOptionDisabled={(option) => !!option.isDisabled}
      maxMenuHeight={200}
      closeMenuOnSelect={false}
    />
  );
};

export default SnackManagerMultiSelect;
