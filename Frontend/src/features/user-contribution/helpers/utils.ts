import type {
  ContributionListType,
  ContributionStoreType,
} from "features/user-contribution/helpers/user-contribution-type";
import { STORAGE_KEY } from "shared/helpers/constants";

// Helper function to transform backend data to normalized structure
export const normalizeStructure = (
  apiResponse: ContributionListType
): ContributionStoreType => {
  const contributions: ContributionStoreType["contributions"] = {};
  const paidSet = new Set<number>();
  const unpaidSet = new Set<number>();

  apiResponse.contributions.forEach((contribution) => {
    const userId = contribution.user_id.toString();
    contributions[userId] = {
      contribution_id: contribution.contribution_id,
      user_id: contribution.user_id,
      user_name: contribution.user_name,
      status: contribution.status ? "paid" : "unpaid",
      pending_status: undefined, // Initialize pending status as undefined
    };

    if (contribution.status) {
      paidSet.add(contribution.contribution_id);
    } else {
      unpaidSet.add(contribution.contribution_id);
    }
  });

  return {
    contributions,
    contribution_status: {
      paid: paidSet,
      unpaid: unpaidSet,
    },
  };
};

// Helper function to load pending changes from localStorage into contributionData
export const loadPendingChangesIntoData = (
  contributionData: ContributionStoreType
): ContributionStoreType => {
  const updatedContributions = { ...contributionData.contributions };

  // Get pending changes from single localStorage key
  const pendingChangesStr = localStorage.getItem(STORAGE_KEY);
  if (pendingChangesStr) {
    try {
      const pendingChanges = JSON.parse(pendingChangesStr);

      Object.entries(pendingChanges).forEach(
        ([userId, changeData]: [string, any]) => {
          if (updatedContributions[userId]) {
            updatedContributions[userId] = {
              ...updatedContributions[userId],
              pending_status: changeData.pending_status,
            };
          }
        }
      );
    } catch (error) {
      console.error(
        "Failed to parse pending changes from localStorage:",
        error
      );
    }
  }

  return {
    ...contributionData,
    contributions: updatedContributions,
  };
};
