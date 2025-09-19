import { create } from "zustand";

import type { EmployeeContribution } from "features/user-contribution/helpers/user-contribution-type";

// localStorage utilities
const STORAGE_KEY = "user-contribution-pending-changes";

export type PendingContributionChanges = {
  [userId: number]: {
    pendingStatus: boolean;
    originalStatus: boolean;
    timestamp: number;
  };
};

const savePendingChangesToStorage = (
  pendingChanges: PendingContributionChanges
): void => {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(pendingChanges));
  } catch (error) {
    console.error("Failed to save pending changes to localStorage:", error);
  }
};

const clearPendingChangesFromStorage = (): void => {
  try {
    localStorage.removeItem(STORAGE_KEY);
  } catch (error) {
    console.error("Failed to clear pending changes from localStorage:", error);
  }
};

export const applyPendingChangesToContributions = (
  contributions: EmployeeContribution[],
  pendingChanges: PendingContributionChanges
): EmployeeContribution[] => {
  return contributions.map((contribution) => {
    const pendingChange = pendingChanges[contribution.user_id];

    if (pendingChange) {
      return {
        ...contribution,
        pendingStatus: pendingChange.pendingStatus,
        hasUnsavedChanges: true,
      };
    }

    return {
      ...contribution,
      pendingStatus: undefined,
      hasUnsavedChanges: false,
    };
  });
};

export const hasPendingChanges = (
  pendingChanges: PendingContributionChanges
): boolean => {
  return Object.keys(pendingChanges).length > 0;
};

type UserContributionStoreType = {
  contributions: EmployeeContribution[];
  isLoading: boolean;
  error: string | null;
  filter: string;
  search: string;
  paidContributions: number;
  unpaidRecords: number;
  selectedContributorIds: number[];
  pendingChanges: PendingContributionChanges;

  // Actions
  setContributions: (contributions: EmployeeContribution[]) => void;
  setLoading: (loading: boolean) => void;
  setError: (error: string | null) => void;
  setFilter: (filter: string) => void;
  setSearch: (search: string) => void;
  setStats: (paidContributions: number, unpaidRecords: number) => void;
  toggleContributor: (userId: number) => void;
  clearSelectedContributors: () => void;
  // New actions for pending changes
  togglePendingStatus: (userId: number, currentStatus: boolean) => void;
  discardPendingChanges: () => void;
  commitPendingChanges: () => void;
  loadPendingChanges: () => void;
};

export const useUserContributionStore = create<UserContributionStoreType>(
  (set, get) => ({
    contributions: [],
    isLoading: false,
    error: null,
    filter: "all",
    search: "",
    paidContributions: 0,
    unpaidRecords: 0,
    selectedContributorIds: [],
    pendingChanges: {},

    setContributions: (contributions) => set({ contributions }),

    setLoading: (isLoading) => set({ isLoading }),

    setError: (error) => set({ error }),

    setFilter: (filter) => set({ filter }),

    setSearch: (search) => set({ search }),

    setStats: (paidContributions, unpaidRecords) =>
      set({ paidContributions, unpaidRecords }),

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

    togglePendingStatus: (userId, currentStatus) => {
      const { pendingChanges } = get();
      const newPendingChanges = { ...pendingChanges };

      const existingChange = newPendingChanges[userId];

      if (existingChange) {
        // If there's already a pending change, toggle it
        const newPendingStatus = !existingChange.pendingStatus;

        // If the new pending status matches the original status, remove the pending change
        if (newPendingStatus === existingChange.originalStatus) {
          delete newPendingChanges[userId];
        } else {
          newPendingChanges[userId] = {
            ...existingChange,
            pendingStatus: newPendingStatus,
            timestamp: Date.now(),
          };
        }
      } else {
        // Create a new pending change
        newPendingChanges[userId] = {
          pendingStatus: !currentStatus,
          originalStatus: currentStatus,
          timestamp: Date.now(),
        };
      }

      savePendingChangesToStorage(newPendingChanges);
      set({ pendingChanges: newPendingChanges });
    },

    discardPendingChanges: () => {
      clearPendingChangesFromStorage();
      set({ pendingChanges: {} });
    },

    commitPendingChanges: () => {
      const { pendingChanges } = get();

      // Prepare selected contributor IDs from pending changes
      const selectedIds = Object.entries(pendingChanges)
        .filter(([_, change]) => change.pendingStatus !== change.originalStatus)
        .map(([userId, _]) => parseInt(userId));

      set({ selectedContributorIds: selectedIds });
    },

    loadPendingChanges: () => {
      const stored = localStorage.getItem(STORAGE_KEY);
      const pendingChanges = stored ? JSON.parse(stored) : {};

      set({ pendingChanges });
    },
  })
);
