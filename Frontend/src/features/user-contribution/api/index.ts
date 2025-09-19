import type {
  BulkUpdatePayload,
  BulkUpdateResponse,
  ContributionResponse,
} from "features/user-contribution/helpers/user-contribution-type";
import API from "shared/helpers/api";

export const getContributions = async (): Promise<ContributionResponse> => {
  const response = await API.get("/contributions");
  return response.data;
};

export const bulkUpdateContributionStatus = async (
  payload: BulkUpdatePayload
): Promise<BulkUpdateResponse> => {
  const response = await API.post("/contributions/bulk-update-status", payload);
  return response.data;
};
