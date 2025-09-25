import type {
  BulkUpdatePayloadType,
  ContributionListType,
} from "features/user-contribution/helpers/user-contribution-type";
import API from "shared/helpers/api";

export const getContributions = async () => {
  const response = await API.get("/contributions");
  return response.data?.data as ContributionListType;
};

export const bulkUpdateContributionStatus = async (
  payload: BulkUpdatePayloadType
) => {
  const response = await API.post("/contributions/bulk-update-status", payload);
  return response.data?.data as ContributionListType;
};
