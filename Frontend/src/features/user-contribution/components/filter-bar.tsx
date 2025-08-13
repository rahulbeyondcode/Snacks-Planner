import React from "react";

type FilterBarProps = {
  activeFilter: string;
  onFilterChange: (filter: string) => void;
  searchValue: string;
  onSearchChange: (value: string) => void;
};

const styles = {
  filterButton:
    "px-3 sm:px-6 cursor-pointer sm:py-2 rounded-lg border-2 border-black font-extrabold text-xs sm:text-sm transition-colors bg-white text-black shadow-[2px_2px_0_0_#000] hover:bg-yellow-200",
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
          className={`${styles.filterButton} ${activeFilter === "all" ? "bg-yellow-300" : ""}`}
          onClick={() => onFilterChange("all")}
        >
          All
        </button>
        <button
          className={`${styles.filterButton} ${activeFilter === "paid" ? "bg-yellow-300" : ""}`}
          onClick={() => onFilterChange("paid")}
        >
          Paid
        </button>
        <button
          className={`${styles.filterButton} ${activeFilter === "unpaid" ? "bg-yellow-300" : ""}`}
          onClick={() => onFilterChange("unpaid")}
        >
          Unpaid
        </button>
      </div>
      <input
        className="border-2 border-black rounded-lg px-3 sm:px-4 py-1.5 sm:py-2 text-black placeholder-black/50 focus:outline-none shadow-[2px_2px_0_0_#000] font-medium text-sm w-72 sm:w-80 md:w-96"
        type="text"
        placeholder="Search Employees"
        value={searchValue}
        onChange={(e) => onSearchChange(e.target.value)}
      />
    </div>
  );
};

export default FilterBar;
