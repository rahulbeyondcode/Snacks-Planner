import { useMutation, useQueryClient } from "@tanstack/react-query";
import React from "react";

import { AlertTriangle } from "lucide-react";

import { bulkUpdateContributionStatus } from "features/user-contribution/api";
import { useUserContributionStore } from "features/user-contribution/store";

const buttonStyles =
  "px-3 py-1.5 cursor-pointer rounded-md border-2 border-black font-semibold text-xs shadow-[2px_2px_0_0_#000] transition-colors disabled:opacity-50 disabled:cursor-not-allowed";

const UnsavedChangesAlert: React.FC = () => {
  const queryClient = useQueryClient();

  const contributionData = useUserContributionStore()?.contributionData;
  const setContributionData = useUserContributionStore()?.setContributionData;
  const commitPendingChanges = useUserContributionStore()?.commitPendingChanges;
  const applyPendingChanges = useUserContributionStore()?.applyPendingChanges;
  const discardPendingChanges =
    useUserContributionStore()?.discardPendingChanges;
  const clearSelectedContributors =
    useUserContributionStore()?.clearSelectedContributors;

  const bulkUpdateMutation = useMutation({
    mutationFn: bulkUpdateContributionStatus,
    onSuccess: (data) => {
      queryClient.setQueryData(["contributions"], data);
      setContributionData(data);
    },
    onError: (error) => {
      console.error("Failed to update contributions:", error);
    },
  });

  // Check for pending changes by looking at contributions with pending_status
  const contributionsWithPendingChanges = contributionData
    ? Object.values(contributionData.contributions).filter(
        (contrib) => contrib.pending_status !== undefined
      )
    : [];

  const hasUnsavedChanges = contributionsWithPendingChanges.length > 0;
  const pendingChangesCount = contributionsWithPendingChanges.length;
  const isSaving = bulkUpdateMutation.isPending;

  const handleSave = async () => {
    if (!hasUnsavedChanges) {
      alert("No changes to save");
      return;
    }

    // Commit pending changes to prepare ALL contributor IDs who should be paid
    commitPendingChanges();

    // Calculate contributor IDs directly to avoid timing issues
    const allPaidContributors = contributionData
      ? Object.values(contributionData.contributions)
          .filter((contribution) => {
            const effectiveStatus =
              contribution.pending_status !== undefined
                ? contribution.pending_status
                : contribution.status;
            return effectiveStatus === "paid";
          })
          .map((contribution) => contribution.user_id)
      : [];

    const contributorIds = allPaidContributors;

    console.log("Sending to API - All paid contributors:", contributorIds);
    console.log("isSaving:", isSaving);

    if (contributorIds.length === 0) {
      alert("No contributors to mark as paid");
      return;
    }

    console.log("About to call bulkUpdateMutation.mutateAsync...");
    try {
      const result = await bulkUpdateMutation.mutateAsync({
        contributors: contributorIds,
      });
      console.log("API call successful, result:", result);

      // Apply pending changes to actual status after successful save
      applyPendingChanges();
      clearSelectedContributors();

      alert("Successfully updated contributions");
    } catch (err) {
      console.error("API call failed:", err);
      const errorMessage =
        err instanceof Error ? err.message : "Failed to update contributions";
      alert(errorMessage);
    }
  };

  const handleDiscard = () => {
    discardPendingChanges();
  };

  if (!hasUnsavedChanges) {
    return null;
  }

  return (
    <footer className="h-[10%]">
      <div className="h-full flex items-center w-[60%] mx-auto">
        <div className="w-full bg-yellow-100 border-2 border-yellow-400 rounded-lg p-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <AlertTriangle className="w-5 h-5 text-yellow-600" />
              <div>
                <h4 className="font-bold text-yellow-800 text-sm">
                  Unsaved Changes
                </h4>
                <p className="text-yellow-700 text-sm">
                  You have {pendingChangesCount} unsaved contribution status{" "}
                  {pendingChangesCount === 1 ? "change" : "changes"}.
                </p>
              </div>
            </div>
            <div className="flex gap-2">
              <button
                onClick={handleDiscard}
                disabled={isSaving}
                className={`${buttonStyles} bg-white text-black hover:bg-gray-100`}
              >
                Discard
              </button>
              <button
                onClick={handleSave}
                disabled={isSaving}
                className={`${buttonStyles} bg-black text-white hover:bg-gray-800`}
              >
                {isSaving ? "Saving..." : "Save Changes"}
              </button>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default UnsavedChangesAlert;
