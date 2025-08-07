import { ErrorMessage } from "@hookform/error-message";
import React from "react";
import { Controller, useFormContext } from "react-hook-form";
import Select from "react-select";

type OptionType = {
  value: string;
  label: string;
  isDisabled?: boolean;
};

type MultiSelectProps = {
  name: string;
  label?: string;
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
  required?: boolean;
};

const MultiSelect: React.FC<MultiSelectProps> = ({
  name,
  label,
  options,
  placeholder = "Select...",
  isMulti = true,
  className = "w-full",
  isDisabled = false,
  classNamePrefix = "react-select",
  closeMenuOnSelect,
  maxMenuHeight = 200,
  isOptionDisabled,
  customError,
  required = false,
}) => {
  const {
    control,
    formState: { errors },
  } = useFormContext();

  return (
    <div className={className}>
      {label && (
        <label htmlFor={name} className="block text-sm font-medium mb-2">
          {label}
          {required && <span className="text-red-500 ml-1">*</span>}
        </label>
      )}
      <Controller
        control={control}
        name={name}
        render={({ field: { value, onChange } }) => {
          // Handle value based on isMulti mode
          const currentValue = isMulti
            ? options.filter((opt) => (value ?? []).includes(opt.value))
            : options.find((opt) => opt.value === value) || null;

          return (
            <Select
              isMulti={isMulti}
              options={options}
              value={currentValue}
              onChange={(selectedOptions) => {
                if (isMulti) {
                  // Multi-select: return array of values
                  onChange(
                    Array.isArray(selectedOptions)
                      ? selectedOptions.map((opt) => opt.value)
                      : []
                  );
                } else {
                  // Single select: return single value or null
                  const singleOption = selectedOptions as OptionType | null;
                  onChange(singleOption ? singleOption.value : null);
                }
              }}
              classNamePrefix={classNamePrefix}
              placeholder={placeholder}
              closeMenuOnSelect={closeMenuOnSelect ?? !isMulti}
              isDisabled={isDisabled}
              maxMenuHeight={maxMenuHeight}
              isOptionDisabled={isOptionDisabled}
            />
          );
        }}
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
