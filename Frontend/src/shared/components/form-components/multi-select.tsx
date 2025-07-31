import React from "react";
import Select from "react-select";
import { Controller, useFormContext } from "react-hook-form";
import { ErrorMessage } from "@hookform/error-message";

type OptionType = {
  value: string;
  label: string;
  isDisabled?: boolean;
};

type MultiSelectProps = {
  name: string;
  options: OptionType[];
  placeholder?: string;
  isMulti?: boolean;
  className?: string;
  isDisabled?: boolean;
  classNamePrefix?: string;
  closeMenuOnSelect?: boolean;
  maxMenuHeight?: number;
  isOptionDisabled?: (option: OptionType) => boolean;
  customError?: string;
};

const MultiSelect: React.FC<MultiSelectProps> = ({
  name,
  options,
  placeholder = "Select...",
  isMulti = true,
  className = "w-full",
  isDisabled = false,
  classNamePrefix = "react-select",
  closeMenuOnSelect = false,
  maxMenuHeight = 200,
  isOptionDisabled,
  customError,
}) => {
  const { control, formState: { errors } } = useFormContext();

  return (
    <div className={className}>
      <Controller
        control={control}
        name={name}
        render={({ field: { value = [], onChange } }) => (
          <Select
            isMulti={isMulti}
            options={options}
            value={options.filter((opt) => (value ?? []).includes(opt.value))}
            onChange={(opts) =>
              onChange(Array.isArray(opts) ? opts.map((o) => o.value) : [])
            }
            classNamePrefix={classNamePrefix}
            placeholder={placeholder}
            closeMenuOnSelect={closeMenuOnSelect}
            isDisabled={isDisabled}
            maxMenuHeight={maxMenuHeight}
            isOptionDisabled={isOptionDisabled}
          />
        )}
      />
      {customError && (
        <small className="text-red-500 mt-1">{customError}</small>
      )}
      <ErrorMessage
        errors={errors}
        name={name}
        render={({ message }) => (
          <small className="text-red-500 mt-1">{message}</small>
        )}
      />
    </div>
  );
};

export { MultiSelect, type OptionType };
