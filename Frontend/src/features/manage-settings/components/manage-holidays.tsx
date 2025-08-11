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
    <div className="bg-white rounded-lg shadow-md">
      <div className="border-b border-gray-200 px-6 py-4">
        <h2 className="text-xl font-semibold text-red-500">Manage Holidays</h2>
      </div>

      <div className="p-6">
        <FormProvider {...methods}>
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-4">
                Choose your working days in a week
              </h3>

              <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                {WEEKDAYS.map((day) => (
                  <CheckboxField
                    key={day}
                    name={day}
                    label={day.charAt(0).toUpperCase() + day.slice(1)}
                    className="flex-col items-start"
                  />
                ))}
              </div>
            </div>

            <div className="pt-4 border-t border-gray-200">
              <button
                type="submit"
                className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
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
