import React from "react";
import Select from "react-select";

type OptionType = {
  value: string;
  label: string;
  isDisabled?: boolean;
};

type PropsType = {
  options: OptionType[];
  selected: string[];
  onChange: (ids: string[]) => void;
  placeholder?: string;
  isMulti?: boolean;
  className?: string;
  isDisabled?: boolean;
  classNamePrefix?: string;
  closeMenuOnSelect?: boolean;
  maxMenuHeight?: number;
  isOptionDisabled?: (option: OptionType) => boolean;
};

const MultiSelect: React.FC<PropsType> = ({
  options,
  selected,
  onChange,
  placeholder = "Select...",
  isMulti = true,
  className = "w-full",
  isDisabled = false,
  classNamePrefix = "react-select",
  closeMenuOnSelect = false,
  maxMenuHeight = 200,
  isOptionDisabled,
}) => {
  const value = options.filter((opt) => selected.includes(opt.value));

  return (
    <Select
      isMulti={isMulti}
      options={options}
      value={value}
      onChange={(opts) =>
        onChange(Array.isArray(opts) ? opts.map((o) => o.value) : [])
      }
      classNamePrefix={classNamePrefix}
      className={className}
      placeholder={placeholder}
      closeMenuOnSelect={closeMenuOnSelect}
      isDisabled={isDisabled}
      maxMenuHeight={maxMenuHeight}
      isOptionDisabled={isOptionDisabled}
    />
  );
};

export { MultiSelect, type OptionType };
