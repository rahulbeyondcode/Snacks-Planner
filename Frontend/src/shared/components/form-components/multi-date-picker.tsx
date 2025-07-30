import { ErrorMessage } from "@hookform/error-message";
import dayjs from "dayjs";
import React from "react";
import { Controller, useFormContext } from "react-hook-form";
import type { DateObject } from "react-multi-date-picker";
import DatePicker from "react-multi-date-picker";

type MultiDatePickerProps = {
  label: string;
  name: string;
  disabledDates?: Date[];
  customError?: string;
  isDisabled?: boolean;
};

const MultiDatePicker: React.FC<MultiDatePickerProps> = ({
  label,
  name,
  disabledDates = [],
  customError,
  isDisabled = false,
}) => {
  const {
    control,
    formState: { errors },
  } = useFormContext();

  // Convert disabledDates to string for react-multi-date-picker
  const disabledList = disabledDates.map((d) => dayjs(d).format("YYYY-MM-DD"));

  return (
    <div className="w-full">
      <label className="block text-sm font-medium mb-2">{label}</label>
      <Controller
        control={control}
        name={name}
        render={({ field: { value, onChange } }) => (
          <DatePicker
            multiple
            value={value}
            onChange={(dates: DateObject[] | DateObject | null) => {
              if (!dates) return onChange([]);

              if (Array.isArray(dates)) {
                onChange(dates.map((date) => date.toDate()));
              } else {
                onChange([dates.toDate()]);
              }
            }}
            format="DD-MMM-YYYY"
            className="w-full border-none focus:ring-0"
            inputClass="w-full border-none focus:ring-0 px-2 py-1"
            disabled={isDisabled}
            mapDays={({ date }) => {
              const formatted = date.format("YYYY-MM-DD");
              if (disabledList.includes(formatted))
                return {
                  disabled: true,
                  style: { color: "#ccc", background: "#f3f3f3" },
                };
              return {};
            }}
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
      {disabledDates.length > 0 && (
        <small className="mt-1 text-gray-500">
          Disabled:{" "}
          {disabledDates.map((d) => dayjs(d).format("DD-MMM-YYYY")).join(", ")}
        </small>
      )}
    </div>
  );
};

export default MultiDatePicker;
