import { useState } from "react";

import EmployeeContributionList from "features/user-contribution/components/employee-contribution-list";
import FilterBar from "features/user-contribution/components/filter-bar";
import Button from "shared/components/save-button";

import type { EmployeeContribution } from "features/user-contribution/helpers/user-contribution-type";

const initialEmployees: EmployeeContribution[] = [
  { id: 1, name: "Ajai Mathew", paid: false },
  { id: 2, name: "Akhil Dileep", paid: true },
  { id: 3, name: "Amal KK", paid: true },
  { id: 4, name: "Rahul R", paid: true },
  { id: 5, name: "Parvathy", paid: false },
  { id: 6, name: "Vishak", paid: false },
];

const UserContributionManagement = () => {
  const [filter, setFilter] = useState<string>("all");
  const [search, setSearch] = useState<string>("");
  const [employees, setEmployees] =
    useState<EmployeeContribution[]>(initialEmployees);

  const filteredEmployees = employees.filter((emp) => {
    if (filter === "paid" && !emp.paid) return false;
    if (filter === "unpaid" && emp.paid) return false;
    if (search && !emp.name.toLowerCase().includes(search.toLowerCase()))
      return false;
    return true;
  });

  const handleTogglePaid = (id: number) => {
    setEmployees((emps) =>
      emps.map((emp) => (emp.id === id ? { ...emp, paid: !emp.paid } : emp))
    );
  };

  const handleSave = () => {
    // Placeholder for save logic
    alert("Saved!");
  };

  return (
    <div className="w-full mx-auto mt-8 md:mt-10 px-3 sm:px-6">
      <div className="bg-white rounded-3xl p-5 sm:p-6 md:p-8 max-w-5xl w-full mx-auto">
        <h2 className="text-xl sm:text-2xl font-extrabold text-black mb-6">
          Employee Contribution
        </h2>
        <FilterBar
          activeFilter={filter}
          onFilterChange={setFilter}
          searchValue={search}
          onSearchChange={setSearch}
        />
        <EmployeeContributionList
          employees={filteredEmployees}
          onTogglePaid={handleTogglePaid}
        />
        <div className="flex justify-end mt-4">
          <Button onClick={handleSave}>Save</Button>
        </div>
      </div>
    </div>
  );
};

export default UserContributionManagement;
