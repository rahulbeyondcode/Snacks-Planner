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
    <>
      <label
        htmlFor={name}
        className={`flex items-center cursor-pointer select-none ${className}`}
      >
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
              className="h-5 w-5 accent-yellow-400 border-2 border-black rounded-sm focus:ring-0 disabled:bg-gray-100 disabled:cursor-not-allowed"
            />
          )}
        />
        {label && (
          <span className="ml-2 text-sm font-extrabold text-black">
            {label}
            {required && <span className="text-red-500 ml-1">*</span>}
          </span>
        )}
      </label>
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
    </>
  );
};

export default CheckboxField;
