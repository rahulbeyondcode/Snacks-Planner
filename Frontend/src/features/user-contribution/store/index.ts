import { create } from "zustand";

import type {
  ContributionListType,
  ContributionStoreType,
} from "features/user-contribution/helpers/user-contribution-type";
import {
  loadPendingChangesIntoData,
  normalizeStructure,
} from "features/user-contribution/helpers/utils";
import { STORAGE_KEY } from "shared/helpers/constants";

type UserContributionStoreType = {
  contributionData: ContributionStoreType | null;
  filter: string;
  search: string;
  selectedContributorIds: number[];

  // Computed properties
  paidContributions: number;
  unpaidRecords: number;

  // Actions
  setContributionData: (data: ContributionListType) => void;
  setFilter: (filter: string) => void;
  setSearch: (search: string) => void;
  toggleContributor: (userId: number) => void;
  clearSelectedContributors: () => void;

  // Atomic update functions
  updateContributionStatus: (
    userId: number,
    newStatus: "paid" | "unpaid"
  ) => void;

  // Pending changes actions
  togglePendingStatus: (
    userId: number,
    currentStatus: "paid" | "unpaid"
  ) => void;
  discardPendingChanges: () => void;
  commitPendingChanges: () => void;
  applyPendingChanges: () => void;
  loadPendingChanges: () => void;
};

export const useUserContributionStore = create<UserContributionStoreType>(
  (set, get) => ({
    contributionData: null,
    filter: "all",
    search: "",
    selectedContributorIds: [],

    // Computed properties - these will be updated when contributionData changes
    paidContributions: 0,
    unpaidRecords: 0,

    setContributionData: (data) => {
      const normalizedData = normalizeStructure(data);
      const dataWithPendingChanges = loadPendingChangesIntoData(normalizedData);

      set({
        contributionData: dataWithPendingChanges,
        paidContributions: dataWithPendingChanges.contribution_status.paid.size,
        unpaidRecords: dataWithPendingChanges.contribution_status.unpaid.size,
      });
    },

    setFilter: (filter) => set({ filter }),

    setSearch: (search) => set({ search }),

    toggleContributor: (userId) => {
      const { selectedContributorIds } = get();
      const isSelected = selectedContributorIds.includes(userId);

      if (isSelected) {
        set({
          selectedContributorIds: selectedContributorIds.filter(
            (id) => id !== userId
          ),
        });
      } else {
        set({
          selectedContributorIds: [...selectedContributorIds, userId],
        });
      }
    },

    clearSelectedContributors: () => set({ selectedContributorIds: [] }),

    // Atomic update function that maintains consistency between contributions and status Sets
    updateContributionStatus: (userId, newStatus) => {
      const { contributionData } = get();
      if (!contributionData) return;

      const userIdStr = userId.toString();
      const contribution = contributionData.contributions[userIdStr];
      if (!contribution) {
        console.error(`User ${userId} not found in contributions`);
        return;
      }

      const contributionId = contribution.contribution_id;
      const currentStatus = contribution.status;

      // No change needed
      if (currentStatus === newStatus) return;

      set((state) => {
        if (!state.contributionData) return state;

        // Create new Sets with the atomic update
        const newPaidSet = new Set(
          state.contributionData.contribution_status.paid
        );
        const newUnpaidSet = new Set(
          state.contributionData.contribution_status.unpaid
        );

        if (newStatus) {
          // Moving to PAID
          newUnpaidSet.delete(contributionId);
          newPaidSet.add(contributionId);
        } else {
          // Moving to UNPAID
          newPaidSet.delete(contributionId);
          newUnpaidSet.add(contributionId);
        }

        const updatedContributionData = {
          ...state.contributionData,
          contributions: {
            ...state.contributionData.contributions,
            [userIdStr]: {
              ...contribution,
              status: newStatus,
              pendingStatus: undefined, // Clear pending status
            },
          },
          contribution_status: {
            paid: newPaidSet,
            unpaid: newUnpaidSet,
          },
        };

        return {
          contributionData: updatedContributionData,
          paidContributions: newPaidSet.size,
          unpaidRecords: newUnpaidSet.size,
        };
      });
    },

    togglePendingStatus: (userId, currentStatus) => {
      console.log("togglePendingStatus called with:", {
        userId,
        currentStatus,
      });
      const { contributionData } = get();
      if (!contributionData) {
        console.log("No contributionData found");
        return;
      }

      const userIdStr = userId.toString();
      const contribution = contributionData.contributions[userIdStr];
      if (!contribution) {
        console.log("No contribution found for user:", userId);
        return;
      }
      console.log("Found contribution:", contribution);

      // Get current pending changes from localStorage
      const pendingChangesStr = localStorage.getItem(STORAGE_KEY);
      let pendingChanges: Record<string, any> = {};

      if (pendingChangesStr) {
        try {
          pendingChanges = JSON.parse(pendingChangesStr);
        } catch (error) {
          console.error("Failed to parse pending changes:", error);
        }
      }

      const currentPendingStatus = contribution.pending_status;
      let newPendingStatus: "paid" | "unpaid" | undefined;

      if (currentPendingStatus === undefined) {
        // No pending change exists, create one
        newPendingStatus = currentStatus === "paid" ? "unpaid" : "paid";
        pendingChanges[userIdStr] = {
          pending_status: newPendingStatus,
          original_status: currentStatus,
          timestamp: new Date().toISOString(),
        };
      } else {
        // Pending change exists, toggle it
        const toggledStatus =
          currentPendingStatus === "paid" ? "unpaid" : "paid";

        if (toggledStatus === currentStatus) {
          // If toggled status matches original, remove pending change
          newPendingStatus = undefined;
          delete pendingChanges[userIdStr];
        } else {
          // Update pending change
          newPendingStatus = toggledStatus;
          pendingChanges[userIdStr] = {
            pending_status: newPendingStatus,
            original_status: currentStatus,
            timestamp: new Date().toISOString(),
          };
        }
      }

      // Save updated pending changes to localStorage
      if (Object.keys(pendingChanges).length > 0) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(pendingChanges));
      } else {
        localStorage.removeItem(STORAGE_KEY);
      }

      console.log("Updated localStorage:", pendingChanges);

      const updatedContribution = {
        ...contribution,
        pending_status: newPendingStatus,
      };
      console.log("Updating contribution:", { userIdStr, updatedContribution });

      set({
        contributionData: {
          ...contributionData,
          contributions: {
            ...contributionData.contributions,
            [userIdStr]: updatedContribution,
          },
        },
      });
    },

    discardPendingChanges: () => {
      // Remove the single pending contribution key from localStorage
      localStorage.removeItem(STORAGE_KEY);

      const { contributionData } = get();
      if (!contributionData) return;

      // Reset all pending_status to undefined
      const updatedContributions = Object.fromEntries(
        Object.entries(contributionData.contributions).map(
          ([userId, contribution]) => [
            userId,
            { ...contribution, pending_status: undefined },
          ]
        )
      );

      set({
        contributionData: {
          ...contributionData,
          contributions: updatedContributions,
        },
      });
    },

    commitPendingChanges: () => {
      console.log("commitPendingChanges called");
      const { contributionData } = get();
      if (!contributionData) {
        console.log("No contributionData in commitPendingChanges");
        return;
      }

      console.log(
        "contributionData.contributions:",
        Object.keys(contributionData.contributions)
      );

      // Prepare selected contributor IDs - ALL users who should be marked as paid
      // This includes: users who are already paid + users with pending "paid" status
      const selectedIds = Object.values(contributionData.contributions)
        .filter((contribution) => {
          const effectiveStatus =
            contribution.pending_status !== undefined
              ? contribution.pending_status
              : contribution.status;
          console.log(
            `User ${contribution.user_id}: status=${contribution.status}, pending=${contribution.pending_status}, effective=${effectiveStatus}`
          );
          return effectiveStatus === "paid";
        })
        .map((contribution) => contribution.user_id);

      console.log("All paid contributors for API:", selectedIds);
      set({ selectedContributorIds: selectedIds });
    },

    // Apply pending changes to actual status and clear pending changes
    applyPendingChanges: () => {
      const { contributionData } = get();
      if (!contributionData) return;

      const updatedContributions = Object.fromEntries(
        Object.entries(contributionData.contributions).map(
          ([userId, contribution]) => {
            // If there's a pending status, apply it to the actual status
            const newStatus =
              contribution.pending_status !== undefined
                ? contribution.pending_status
                : contribution.status;

            return [
              userId,
              {
                ...contribution,
                status: newStatus,
                pending_status: undefined, // Clear pending status
              },
            ];
          }
        )
      );

      // Also update the contribution_status sets
      const paidSet = new Set<number>();
      const unpaidSet = new Set<number>();

      Object.values(updatedContributions).forEach((contribution) => {
        if (contribution.status === "paid") {
          paidSet.add(contribution.contribution_id);
        } else {
          unpaidSet.add(contribution.contribution_id);
        }
      });

      // Clear localStorage
      localStorage.removeItem(STORAGE_KEY);

      set({
        contributionData: {
          ...contributionData,
          contributions: updatedContributions,
          contribution_status: {
            paid: paidSet,
            unpaid: unpaidSet,
          },
        },
      });

      console.log("Applied all pending changes to actual status");
    },

    loadPendingChanges: () => {
      const { contributionData } = get();
      if (!contributionData) return;

      const dataWithPendingChanges =
        loadPendingChangesIntoData(contributionData);
      set({ contributionData: dataWithPendingChanges });
    },
  })
);
