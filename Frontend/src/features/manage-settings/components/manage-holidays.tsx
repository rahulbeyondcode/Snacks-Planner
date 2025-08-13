import React from "react";
import { FormProvider, useForm } from "react-hook-form";

import CheckboxField from "shared/components/form-components/checkbox-field";

const WEEKDAYS = [
  "sunday",
  "monday",
  "tuesday",
  "wednesday",
  "thursday",
  "friday",
  "saturday",
] as const;

type WorkingDaysFormData = {
  sunday: boolean;
  monday: boolean;
  tuesday: boolean;
  wednesday: boolean;
  thursday: boolean;
  friday: boolean;
  saturday: boolean;
};

const ManageHolidays: React.FC = () => {
  const methods = useForm<WorkingDaysFormData>({
    defaultValues: {
      sunday: false,
      monday: true,
      tuesday: true,
      wednesday: true,
      thursday: true,
      friday: true,
      saturday: false,
    },
  });

  const { handleSubmit } = methods;

  const onSubmit = (data: WorkingDaysFormData) => {
    console.log("Working days updated:", data);
    // Here you would typically save the data via API
  };

  return (
    <div className="bg-white rounded-2xl border-2 border-black shadow-[6px_6px_0_0_#000]">
      <div className="border-b-2 border-black px-4 sm:px-6 py-3 sm:py-4">
        <h2 className="text-xl sm:text-2xl font-extrabold text-black">
          Manage Holidays
        </h2>
      </div>

      <div className="p-4 sm:p-6">
        <FormProvider {...methods}>
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
            <div>
              <h3 className="text-base sm:text-lg font-semibold text-black mb-4">
                Choose your working days in a week
              </h3>

              <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 sm:gap-4">
                {WEEKDAYS.map((day) => (
                  <CheckboxField
                    key={day}
                    name={day}
                    label={day.charAt(0).toUpperCase() + day.slice(1)}
                    className="bg-white rounded-lg border-2 border-black px-3 py-2 shadow-[2px_2px_0_0_#000] gap-2 hover:bg-yellow-50"
                  />
                ))}
              </div>
            </div>

            <div className="pt-4 border-t-2 border-dashed border-black/20">
              <button
                type="submit"
                className="inline-flex items-center px-4 py-2 rounded-lg border-2 border-black bg-yellow-300 text-black font-extrabold shadow-[2px_2px_0_0_#000] hover:bg-yellow-400 focus:outline-none"
              >
                Save Working Days
              </button>
            </div>
          </form>
        </FormProvider>
      </div>
    </div>
  );
};

export default ManageHolidays;
