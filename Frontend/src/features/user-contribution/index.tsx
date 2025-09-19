import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { useEffect, useMemo } from "react";

import EmployeeContributionList from "features/user-contribution/components/employee-contribution-list";
import FilterBar from "features/user-contribution/components/filter-bar";
import UnsavedChangesAlert from "features/user-contribution/components/unsaved-changes-alert";

import {
  bulkUpdateContributionStatus,
  getContributions,
} from "features/user-contribution/api";
import {
  applyPendingChangesToContributions,
  hasPendingChanges,
  useUserContributionStore,
} from "features/user-contribution/store";
import {
  GET_CONTRIBUTIONS_RETRY,
  GET_CONTRIBUTIONS_STALE_TIME,
} from "shared/helpers/constants";

const UserContributionManagement = () => {
  const {
    filter,
    search,
    pendingChanges,
    setFilter,
    setSearch,
    setStats,
    togglePendingStatus,
    discardPendingChanges,
    commitPendingChanges,
    loadPendingChanges,
    clearSelectedContributors,
  } = useUserContributionStore();

  const queryClient = useQueryClient();

  // React Query hooks
  const {
    data: contributionData,
    isLoading,
    error,
  } = useQuery({
    queryKey: ["contributions"],
    queryFn: getContributions,
    staleTime: GET_CONTRIBUTIONS_STALE_TIME,
    retry: GET_CONTRIBUTIONS_RETRY,
  });

  const bulkUpdateMutation = useMutation({
    mutationFn: bulkUpdateContributionStatus,
    onSuccess: (data) => {
      queryClient.setQueryData(["contributions"], data);
    },
    onError: (error) => {
      console.error("Failed to update contributions:", error);
    },
  });

  // Process contributions with pending changes
  const contributionsWithPending = useMemo(() => {
    if (!contributionData?.data?.contributions) return [];
    return applyPendingChangesToContributions(
      contributionData.data.contributions,
      pendingChanges
    );
  }, [contributionData?.data?.contributions, pendingChanges]);

  const filteredEmployees = useMemo(() => {
    return contributionsWithPending.filter((emp) => {
      const displayStatus =
        emp.pendingStatus !== undefined ? emp.pendingStatus : emp.status;

      if (filter === "paid" && !displayStatus) return false;
      if (filter === "unpaid" && displayStatus) return false;
      if (search && !emp.user_name.toLowerCase().includes(search.toLowerCase()))
        return false;
      return true;
    });
  }, [contributionsWithPending, filter, search]);

  const hasUnsavedChanges = hasPendingChanges(pendingChanges);
  const pendingChangesCount = Object.keys(pendingChanges).length;

  const handleTogglePaid = (userId: number) => {
    const employee = contributionsWithPending.find(
      (emp) => emp.user_id === userId
    );
    if (employee) {
      togglePendingStatus(userId, employee.status);
    }
  };

  const handleSave = async () => {
    if (!hasUnsavedChanges) {
      alert("No changes to save");
      return;
    }

    // Commit pending changes to prepare selected contributor IDs
    commitPendingChanges();

    // Get the contributor IDs that have pending status changes
    const contributorIds = Object.entries(pendingChanges)
      .filter(([_, change]) => change.pendingStatus !== change.originalStatus)
      .map(([userId, _]) => parseInt(userId));

    if (contributorIds.length === 0) {
      alert("No valid changes to save");
      return;
    }

    try {
      await bulkUpdateMutation.mutateAsync({
        contributors: contributorIds,
      });

      // Clear pending changes after successful save
      discardPendingChanges();
      clearSelectedContributors();

      alert("Successfully updated contributions");
    } catch (err) {
      const errorMessage =
        err instanceof Error ? err.message : "Failed to update contributions";
      alert(errorMessage);
    }
  };

  const handleDiscardChanges = () => {
    discardPendingChanges();
  };

  // Load pending changes from localStorage on component mount
  useEffect(() => {
    loadPendingChanges();
  }, [loadPendingChanges]);

  // Update stats when data is available
  useEffect(() => {
    if (contributionData?.data) {
      setStats(
        contributionData.data.paid_contributions,
        contributionData.data.unpaid_records
      );
    }
  }, [contributionData?.data, setStats]);

  if (isLoading && !contributionData) {
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
      <header className="h-20 flex-shrink-0 border-b border-gray-200">
        <div className="max-w-5xl mx-auto px-4 h-full">
          <h2 className="text-xl sm:text-2xl font-extrabold m-0 leading-none mb-5">
            Employee Contribution
          </h2>

          <FilterBar
            activeFilter={filter}
            onFilterChange={setFilter}
            searchValue={search}
            onSearchChange={setSearch}
          />
        </div>
      </header>

      <main className="flex-1 min-h-0 overflow-y-auto">
        <div className="max-w-5xl mx-auto px-4 py-4 min-h-0">
          <EmployeeContributionList
            employees={filteredEmployees}
            onTogglePaid={handleTogglePaid}
          />
        </div>
      </main>

      <footer className="h-20 flex-shrink-0">
        <div className="px-4 h-full w-[60%] mx-auto">
          {hasUnsavedChanges && (
            <UnsavedChangesAlert
              isVisible={hasUnsavedChanges}
              pendingChangesCount={pendingChangesCount}
              onSave={handleSave}
              onDiscard={handleDiscardChanges}
              isSaving={bulkUpdateMutation.isPending}
            />
          )}
        </div>
      </footer>
    </div>
  );
};

export default UserContributionManagement;
