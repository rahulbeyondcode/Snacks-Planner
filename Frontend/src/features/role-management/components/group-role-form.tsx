import { yupResolver } from "@hookform/resolvers/yup";
import React from "react";
import {
  Controller,
  FormProvider,
  useFieldArray,
  useForm,
} from "react-hook-form";
import { HiOutlineArchiveBoxXMark } from "react-icons/hi2";
import { v4 as uuidv4 } from "uuid";

import { MultiSelect } from "shared/components/form-components/multi-select";
import Button from "shared/components/save-button";

import { schema } from "features/role-management/components/form-config";
import type { Employee, Group } from "features/role-management/types";

// Mock employee data (replace with API integration later)
const initialEmployees: Employee[] = [
  { id: "1", name: "Rahul R", email: "rahul@email.com" },
  { id: "2", name: "Sojo S", email: "sojo@email.com" },
  { id: "3", name: "Amal KK", email: "amal@email.com" },
  { id: "4", name: "Jijo J", email: "jijo@email.com" },
  { id: "5", name: "Arjun A", email: "arjun@email.com" },
  { id: "6", name: "Ajai Mathew", email: "ajai@email.com" },
  { id: "7", name: "Parvathy", email: "parvathy@email.com" },
  { id: "8", name: "Vishak", email: "vishak@email.com" },
];

const GroupRoleForm: React.FC = () => {
  const defaultValues = { groups: [] as Group[] };

  const methods = useForm({
    defaultValues,
    resolver: yupResolver(schema),
    mode: "all",
  });

  const {
    control,
    handleSubmit,
    formState: { errors },
    watch,
    trigger,
  } = methods;

  const { fields, append, remove } = useFieldArray({
    control,
    name: "groups",
  });

  // Accordion open state for group list
  const [openIndex, setOpenIndex] = React.useState<number | null>(0);

  const onSubmit = () => {
    // TODO: Integrate with backend
    alert("Groups saved! (mock)");
  };

  // Use the mock employee list for selects
  const employees = initialEmployees;

  // Add new group only if all existing groups are valid
  const handleAddGroup = async () => {
    const valid = await trigger("groups");
    if (valid) {
      append({ id: uuidv4(), name: "", memberIds: [], snackManagerIds: [] });
    }
  };

  const employeesOptions = employees.map((e) => ({
    value: e.id,
    label: e.name,
  }));

  return (
    <FormProvider {...methods}>
      <form
        className="max-w-2xl mx-auto mt-10 p-6 rounded-lg border border-black bg-white"
        onSubmit={handleSubmit(onSubmit)}
      >
        <h2 className="text-lg font-semibold mb-4">Manage groups</h2>
        {/* Accordion Group List */}
        <div className="space-y-4 mb-8">
          {fields.map((field, index: number) => {
            // Example: months for demo, in real use attach month to group data
            const months = ["This month", "August", "September", "October"];
            const month = months[index] || "";
            const isCurrent = index === 0;
            const isOpen = openIndex === index;

            return (
              <div
                key={field.id}
                className={`rounded-xl border shadow-sm transition-all duration-200 ${isCurrent ? "bg-green-100 border-green-400" : "bg-orange-100 border-orange-300"}`}
              >
                {/* Accordion Summary Row */}
                <div
                  className="flex items-center justify-between px-6 py-4 cursor-pointer select-none"
                  onClick={() => setOpenIndex(isOpen ? null : index)}
                >
                  <div className="flex items-center gap-4">
                    <svg
                      width="24"
                      height="24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      className="text-gray-500"
                    >
                      <rect x="4" y="6" width="16" height="2" rx="1" />
                      <rect x="4" y="11" width="16" height="2" rx="1" />
                      <rect x="4" y="16" width="16" height="2" rx="1" />
                    </svg>
                    <div>
                      <div
                        className={`font-semibold text-lg ${isCurrent ? "text-green-800" : "text-orange-700"}`}
                      >
                        {field.name || `Group ${index + 1}`}
                      </div>
                      <div className="text-xs text-gray-500">{month}</div>
                    </div>
                  </div>
                  <div className="flex items-center gap-3">
                    <Button
                      type="button"
                      className="border border-red-500 text-red-500 p-2 rounded-3xl"
                      shouldUseDefaultClass={false}
                      onClick={(e) => {
                        e.stopPropagation();
                        remove(index);
                      }}
                    >
                      <HiOutlineArchiveBoxXMark />
                    </Button>
                  </div>
                </div>
                {/* Accordion Panel */}
                {isOpen && (
                  <div className="px-6 pb-4">
                    <div className="mb-2 flex items-center">
                      <label className="w-40">Enter group name</label>
                      <Controller
                        control={control}
                        name={`groups.${index}.name`}
                        render={({ field }: { field: any }) => (
                          <input
                            type="text"
                            className="border rounded px-2 py-1 w-full ml-2"
                            {...field}
                            placeholder={`Group ${index + 1}`}
                          />
                        )}
                      />
                    </div>
                    {errors.groups?.[index]?.name && (
                      <div className="text-red-500 text-xs mb-2 ml-40">
                        {errors.groups[index]?.name?.message || ""}
                      </div>
                    )}
                    <div className="mb-2 flex items-center">
                      <label className="w-40">Choose members</label>
                      <MultiSelect
                        name={`groups.${index}.memberIds`}
                        placeholder="Select members..."
                        options={employeesOptions.filter(
                          (opt) =>
                            !fields.some(
                              (g: any, gIndex: number) =>
                                (g.memberIds ?? []).includes(opt.value) &&
                                gIndex !== index
                            )
                        )}
                      />
                    </div>
                    {errors.groups?.[index]?.memberIds && (
                      <div className="text-red-500 text-xs mb-2 ml-40">
                        {errors.groups[index]?.memberIds?.message || ""}
                      </div>
                    )}
                    <div className="mb-2 flex items-center">
                      <label className="w-40">Choose snack manager</label>
                      <MultiSelect
                        name={`groups.${index}.snackManagerIds`}
                        placeholder="Select snack managers..."
                        options={employeesOptions.filter((e) =>
                          (
                            (watch(`groups.${index}.memberIds`) as
                              | string[]
                              | undefined) ?? []
                          ).includes(e.value)
                        )}
                      />
                    </div>
                    {errors.groups?.[index]?.snackManagerIds && (
                      <div className="text-red-500 text-xs mb-2 ml-40">
                        {errors.groups[index]?.snackManagerIds?.message || ""}
                      </div>
                    )}
                  </div>
                )}
              </div>
            );
          })}
        </div>
        <button
          className="w-full border border-black rounded-lg py-2 mt-2 bg-gray-100 hover:bg-gray-200"
          onClick={handleAddGroup}
          type="button"
        >
          + Add New Group
        </button>
        <div className="flex justify-center mt-6">
          <Button onClick={handleSubmit(onSubmit)}>Save</Button>
        </div>
        {errors.groups && typeof errors.groups.message === "string" && (
          <div className="text-red-600 mt-2 text-center">
            {errors.groups.message}
          </div>
        )}
      </form>
    </FormProvider>
  );
};

export default GroupRoleForm;
