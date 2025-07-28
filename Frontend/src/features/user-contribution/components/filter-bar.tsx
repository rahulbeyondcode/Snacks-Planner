import React from "react";

type FilterBarProps = {
  activeFilter: string;
  onFilterChange: (filter: string) => void;
  searchValue: string;
  onSearchChange: (value: string) => void;
};

const styles = {
  filterButton:
    "px-6 py-2 rounded-lg border-2 border-orange-400 font-medium text-sm transition-colors bg-white text-orange-500",
};

const FilterBar: React.FC<FilterBarProps> = ({
  activeFilter,
  onFilterChange,
  searchValue,
  onSearchChange,
}) => {
  return (
    <div className="flex items-center justify-between mb-6">
      <div className="flex gap-3">
        <button
          className={`${styles.filterButton} ${activeFilter === "all" && "bg-pink-200 text-orange-800"}`}
          onClick={() => onFilterChange("all")}
        >
          All
        </button>
        <button
          className={`${styles.filterButton} ${activeFilter === "paid" && "bg-yellow-100 text-orange-800"}`}
          onClick={() => onFilterChange("paid")}
        >
          Paid
        </button>
        <button
          className={`${styles.filterButton} ${activeFilter === "unpaid" && "bg-yellow-100 text-orange-800"}`}
          onClick={() => onFilterChange("unpaid")}
        >
          Unpaid
        </button>
      </div>
      <input
        className="border-2 border-orange-400 rounded-lg px-6 py-2 text-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-300 placeholder-orange-400 font-medium text-sm"
        type="text"
        placeholder="Search Employees"
        value={searchValue}
        onChange={(e) => onSearchChange(e.target.value)}
        style={{ width: 220 }}
      />
    </div>
  );
};

export default FilterBar;
