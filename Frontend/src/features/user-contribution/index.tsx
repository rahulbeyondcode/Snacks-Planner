import { useQuery } from "@tanstack/react-query";
import isEmpty from "lodash.isempty";
import { useEffect } from "react";

import EmployeeContributionList from "features/user-contribution/components/employee-contribution-list";
import FilterBar from "features/user-contribution/components/filter-bar";
import UnsavedChangesAlert from "features/user-contribution/components/unsaved-changes-alert";

import { getContributions } from "features/user-contribution/api";
import { useUserContributionStore } from "features/user-contribution/store";
import {
  GET_CONTRIBUTIONS_RETRY,
  GET_CONTRIBUTIONS_STALE_TIME,
} from "shared/helpers/constants";

const UserContributionManagement = () => {
  const { setContributionData, loadPendingChanges } =
    useUserContributionStore();

  const {
    data: apiContributionData,
    isLoading,
    error,
  } = useQuery({
    queryKey: ["contributions"],
    queryFn: getContributions,
    staleTime: GET_CONTRIBUTIONS_STALE_TIME,
    retry: GET_CONTRIBUTIONS_RETRY,
  });

  // Update store when data changes
  useEffect(() => {
    if (apiContributionData) {
      setContributionData(apiContributionData);
    }
  }, [apiContributionData, setContributionData]);

  // Load pending changes from localStorage on component mount
  useEffect(() => {
    loadPendingChanges();
  }, [loadPendingChanges]);

  if (isLoading && isEmpty(apiContributionData)) {
    return (
      <div className="h-full flex items-center justify-center">
        <div className="text-lg font-semibold text-gray-600">
          Loading contributions...
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="h-full flex items-center justify-center">
        <div className="text-lg font-semibold text-red-600">
          Error loading contributions:{" "}
          {error instanceof Error ? error.message : "Unknown error"}
        </div>
      </div>
    );
  }

  return (
    <div className="h-full flex flex-col gap-4 box-border">
      <header className="h-[15%] border-b border-gray-200">
        <div className="max-w-5xl mx-auto h-full flex flex-col justify-center">
          <h2 className="text-xl sm:text-2xl font-extrabold m-0 leading-none mb-4">
            Employee Contribution
          </h2>

          <FilterBar />
        </div>
      </header>

      <main className="flex-1 overflow-y-auto border-b border-gray-200">
        <div className="max-w-5xl mx-auto">
          <EmployeeContributionList />
        </div>
      </main>

      <UnsavedChangesAlert />
    </div>
  );
};

export default UserContributionManagement;
