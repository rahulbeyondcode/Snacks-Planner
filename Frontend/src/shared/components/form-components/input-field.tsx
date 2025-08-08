import { ErrorMessage } from "@hookform/error-message";
import React from "react";
import { Controller, useFormContext } from "react-hook-form";

type InputFieldProps = {
  name: string;
  label?: string;
  placeholder?: string;
  type?: "text" | "email" | "password" | "number" | "tel" | "url";
  className?: string;
  isDisabled?: boolean;
  customError?: string;
  autoComplete?: string;
  maxLength?: number;
  minLength?: number;
  pattern?: string;
  required?: boolean;
};

const InputField: React.FC<InputFieldProps> = ({
  name,
  label,
  placeholder = "",
  type = "text",
  className = "w-full",
  isDisabled = false,
  customError,
  autoComplete,
  maxLength,
  minLength,
  pattern,
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
        render={({ field: { value = "", onChange, onBlur } }) => (
          <input
            id={name}
            type={type}
            value={value}
            onChange={onChange}
            onBlur={onBlur}
            placeholder={placeholder}
            disabled={isDisabled}
            autoComplete={autoComplete}
            maxLength={maxLength}
            minLength={minLength}
            pattern={pattern}
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
          />
        )}
      />
      {customError && (
        <small className="text-red-500 mt-1 block">{customError}</small>
      )}
      <ErrorMessage
        errors={errors}
        name={name}
        render={({ message }) => (
          <small className="text-red-500 mt-1 block">{message}</small>
        )}
      />
    </div>
  );
};

export default InputField;
