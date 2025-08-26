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
  multiDatePickMode?: boolean;
  placeholder?: string;
};

const MultiDatePicker: React.FC<MultiDatePickerProps> = ({
  label,
  name,
  disabledDates = [],
  customError,
  isDisabled = false,
  multiDatePickMode = false,
  placeholder = "Select a date",
}) => {
  const {
    control,
    formState: { errors },
  } = useFormContext();

  // Convert disabledDates to string for react-multi-date-picker
  const disabledList = disabledDates.map((d) => dayjs(d).format("YYYY-MM-DD"));

  return (
    <div className="w-full">
      <label className="block text-sm font-medium mb-2 text-black">
        {label}
      </label>
      <Controller
        control={control}
        name={name}
        render={({ field: { value, onChange } }) => (
          <DatePicker
            placeholder={placeholder}
            multiple={multiDatePickMode}
            value={multiDatePickMode ? value : value?.[0] || null}
            onChange={(dates: DateObject[] | DateObject | null) => {
              if (!dates) return onChange(multiDatePickMode ? [] : null);

              if (multiDatePickMode) {
                if (Array.isArray(dates)) {
                  onChange(dates.map((date) => date.toDate()));
                } else {
                  onChange([dates.toDate()]);
                }
              } else {
                if (Array.isArray(dates)) {
                  onChange(dates[0]?.toDate() || null);
                } else {
                  onChange(dates.toDate());
                }
              }
            }}
            format="DD-MMM-YYYY"
            className="w-full"
            inputClass="w-full border-2 border-black rounded-lg px-3 py-2.5 bg-white shadow-[2px_2px_0_0_#000] focus:outline-none focus:ring-0 disabled:bg-gray-50 disabled:cursor-not-allowed"
            disabled={isDisabled}
            mapDays={({ date }) => {
              const formatted = date.format("YYYY-MM-DD");
              if (disabledList.includes(formatted))
                return {
                  disabled: true,
                  style: {
                    color: "#9ca3af",
                    background: "#f9fafb",
                    textDecoration: "line-through",
                  },
                };
              return {};
            }}
            style={{
              width: "100%",
            }}
            calendarPosition="bottom-left"
            containerStyle={{
              width: "100%",
            }}
          />
        )}
      />
      {customError && (
        <small className="text-red-600 text-xs mt-1 block">{customError}</small>
      )}

      <ErrorMessage
        errors={errors}
        name={name}
        render={({ message }) => (
          <small className="text-red-600 text-xs mt-1 block">{message}</small>
        )}
      />
    </div>
  );
};

export default MultiDatePicker;
