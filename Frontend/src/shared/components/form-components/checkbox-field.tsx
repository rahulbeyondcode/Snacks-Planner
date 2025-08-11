import { ErrorMessage } from "@hookform/error-message";
import React from "react";
import { Controller, useFormContext } from "react-hook-form";

type CheckboxFieldProps = {
  name: string;
  label?: string;
  className?: string;
  isDisabled?: boolean;
  customError?: string;
  required?: boolean;
};

const CheckboxField: React.FC<CheckboxFieldProps> = ({
  name,
  label,
  className = "",
  isDisabled = false,
  customError,
  required = false,
}) => {
  const {
    control,
    formState: { errors },
  } = useFormContext();

  return (
    <div className={`flex items-center ${className}`}>
      <Controller
        control={control}
        name={name}
        render={({ field: { value = false, onChange, onBlur } }) => (
          <input
            id={name}
            type="checkbox"
            checked={value}
            onChange={(e) => onChange(e.target.checked)}
            onBlur={onBlur}
            disabled={isDisabled}
            className="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:bg-gray-100 disabled:cursor-not-allowed"
          />
        )}
      />
      {label && (
        <label
          htmlFor={name}
          className="ml-2 text-sm font-medium text-gray-900"
        >
          {label}
          {required && <span className="text-red-500 ml-1">*</span>}
        </label>
      )}
      {customError && (
        <small className="text-red-500 mt-1 block ml-6">{customError}</small>
      )}
      <ErrorMessage
        errors={errors}
        name={name}
        render={({ message }) => (
          <small className="text-red-500 mt-1 block ml-6">{message}</small>
        )}
      />
    </div>
  );
};

export default CheckboxField;
