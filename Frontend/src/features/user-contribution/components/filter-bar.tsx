import { useUserContributionStore } from "features/user-contribution/store";

const styles = {
  filterButton:
    "px-3 sm:px-6 cursor-pointer sm:py-2 rounded-full border-2 border-black font-extrabold text-xs sm:text-sm transition-colors bg-white text-black shadow-[2px_2px_0_0_#000] hover:bg-yellow-200",
};

const FilterBar = () => {
  const filter = useUserContributionStore()?.filter;
  const search = useUserContributionStore()?.search;
  const setFilter = useUserContributionStore()?.setFilter;
  const setSearch = useUserContributionStore()?.setSearch;
  const paidContributions =
    useUserContributionStore()?.contributionData?.contribution_status?.paid
      ?.size;
  const unpaidContributions =
    useUserContributionStore()?.contributionData?.contribution_status?.unpaid
      ?.size;

  return (
    <div className="flex items-center justify-between">
      <div className="flex gap-3">
        <button
          className={`${styles.filterButton} ${filter === "all" ? "bg-yellow-300" : ""}`}
          onClick={() => setFilter("all")}
        >
          All
        </button>
        <button
          className={`${styles.filterButton} ${filter === "paid" ? "bg-yellow-300" : ""}`}
          onClick={() => setFilter("paid")}
        >
          Paid ({paidContributions})
        </button>
        <button
          className={`${styles.filterButton} ${filter === "unpaid" ? "bg-yellow-300" : ""}`}
          onClick={() => setFilter("unpaid")}
        >
          Unpaid ({unpaidContributions})
        </button>
      </div>
      <input
        className="border-2 border-black rounded-lg px-3 sm:px-4 py-1.5 sm:py-2 text-black placeholder-black/50 focus:outline-none shadow-[2px_2px_0_0_#000] font-medium text-sm w-72 flex-1 ms-10"
        type="text"
        placeholder="Search Employees"
        value={search}
        onChange={(e) => setSearch(e.target.value)}
      />
    </div>
  );
};

export default FilterBar;
