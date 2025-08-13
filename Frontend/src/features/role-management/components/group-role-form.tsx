import { yupResolver } from "@hookform/resolvers/yup";
import { ArchiveX } from "lucide-react";
import React from "react";
import {
  Controller,
  FormProvider,
  useFieldArray,
  useForm,
} from "react-hook-form";
import { v4 as uuidv4 } from "uuid";

import { MultiSelect } from "shared/components/form-components/multi-select";
import Button from "shared/components/save-button";

import { schema } from "features/role-management/components/form-config";
import type {
  Employee,
  Group,
} from "features/role-management/helpers/role-management-types";

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
        className="w-full max-w-6xl mt-4 sm:mt-6 px-0"
        onSubmit={handleSubmit(onSubmit)}
      >
        <h2 className="text-2xl sm:text-3xl font-extrabold text-black mb-4">
          Manage groups
        </h2>
        {/* Accordion Group List */}
        <div className="space-y-4 sm:space-y-5 mb-8">
          {fields.map((field, index: number) => {
            // Example: months for demo, in real use attach month to group data
            const months = ["This month", "August", "September", "October"];
            const month = months[index] || "";
            const isCurrent = index === 0;
            const isOpen = openIndex === index;

            return (
              <div
                key={field.id}
                className={`bg-white rounded-2xl border-2 border-black shadow-[6px_6px_0_0_#000] transition-all duration-200`}
              >
                {/* Accordion Summary Row */}
                <div
                  className="flex items-center justify-between px-4 sm:px-6 py-4 cursor-pointer select-none border-b-2 border-black"
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
                      <div className="font-extrabold text-xl text-black">
                        {field.name || `Group ${index + 1}`}
                      </div>
                      <div className="text-[10px] font-bold inline-flex items-center gap-1 px-2 py-0.5 rounded-md border-2 border-black bg-yellow-200 text-black mt-1 shadow-[2px_2px_0_0_#000]">
                        {isCurrent ? "Current" : month}
                      </div>
                    </div>
                  </div>
                  <div className="flex items-center gap-3">
                    <Button
                      type="button"
                      className="bg-red-300 text-black border-2 border-black p-2 rounded-lg shadow-[2px_2px_0_0_#000] hover:bg-red-400"
                      shouldUseDefaultClass={false}
                      onClick={(e) => {
                        e.stopPropagation();
                        remove(index);
                      }}
                    >
                      <ArchiveX />
                    </Button>
                  </div>
                </div>
                {/* Accordion Panel */}
                {isOpen && (
                  <div className="px-4 sm:px-6 pb-5">
                    <div className="mb-4 flex items-center">
                      <label className="w-40 text-sm font-semibold text-black/80">
                        Enter group name
                      </label>
                      <Controller
                        control={control}
                        name={`groups.${index}.name`}
                        render={({ field }: { field: any }) => (
                          <input
                            type="text"
                            className="border-2 border-black rounded-lg px-3 py-2.5 w-full ml-2 focus:outline-none focus:ring-0 bg-white"
                            {...field}
                            placeholder={`Group ${index + 1}`}
                          />
                        )}
                      />
                    </div>
                    {errors.groups?.[index]?.name && (
                      <div className="text-red-600 text-xs font-semibold mb-3 ml-40">
                        {errors.groups[index]?.name?.message || ""}
                      </div>
                    )}
                    <div className="mb-4 flex items-center">
                      <label className="w-40 text-sm font-semibold text-black/80">
                        Choose members
                      </label>
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
                      <div className="text-red-600 text-xs font-semibold mb-3 ml-40">
                        {errors.groups[index]?.memberIds?.message || ""}
                      </div>
                    )}
                    <div className="mb-4 flex items-center">
                      <label className="w-40 text-sm font-semibold text-black/80">
                        Choose snack manager
                      </label>
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
                      <div className="text-red-600 text-xs font-semibold mb-2 ml-40">
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
          className="w-full rounded-xl py-3 sm:py-3 mt-1 bg-yellow-300 text-black border-2 border-black font-extrabold shadow-[3px_3px_0_0_#000] hover:bg-yellow-400"
          onClick={handleAddGroup}
          type="button"
        >
          + Add New Group
        </button>
        <div className="flex justify-center mt-6">
          <Button onClick={handleSubmit(onSubmit)}>Save</Button>
        </div>
        {errors.groups && typeof errors.groups.message === "string" && (
          <div className="text-red-600 mt-2 text-center font-semibold">
            {errors.groups.message}
          </div>
        )}
      </form>
    </FormProvider>
  );
};

export default GroupRoleForm;
