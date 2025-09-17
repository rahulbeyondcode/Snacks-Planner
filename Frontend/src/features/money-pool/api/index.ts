import type {
  BlockedFundPayloadType,
  BlockedFundType,
  MoneyPoolPayloadType,
  MoneyPoolType,
} from "features/money-pool/helpers/money-pool-types";
import API from "shared/helpers/api";

const getMoneyPool = async (): Promise<MoneyPoolType> => {
  const response = await API.get("/money-pool");
  return response.data?.data;
};

const updateMoneyPoolSettings = async (
  payload: MoneyPoolPayloadType
): Promise<MoneyPoolType> => {
  const response = await API.put("/money-pool-settings", payload);
  return response.data;
};

const createBlockedFund = async (
  payload: BlockedFundPayloadType
): Promise<BlockedFundType> => {
  const response = await API.post("/money-pool-blocks", payload);
  return response.data;
};

const updateBlockedFund = async (
  blockId: number,
  payload: BlockedFundPayloadType
): Promise<BlockedFundType> => {
  const response = await API.put(`/money-pool-blocks/${blockId}`, payload);
  return response.data;
};

const deleteBlockedFund = async (blockId: number): Promise<void> => {
  await API.delete(`/money-pool-blocks/${blockId}`);
};

export {
  createBlockedFund,
  deleteBlockedFund,
  getMoneyPool,
  updateBlockedFund,
  updateMoneyPoolSettings,
};
