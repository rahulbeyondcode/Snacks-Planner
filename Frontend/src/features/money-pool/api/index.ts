import type {
  BlockedFundType,
  MoneyPoolType,
} from "features/money-pool/helpers/money-pool-types";
import API from "shared/helpers/api";

const getMoneyPool = async (): Promise<MoneyPoolType> => {
  // const response = await API.get("/money-pools");
  const response = await API.get("/money-pool-settings");
  return response.data;
};

const updateMoneyPool = async (apiData: {
  per_month_amount: number;
  multiplier: number;
}): Promise<MoneyPoolType> => {
  const response = await API.put("/money-pool-settings", apiData);
  return response.data;
};

const createBlockedFund = async (apiData: {
  reason: string;
  block_date: string;
  amount: number;
}): Promise<BlockedFundType> => {
  const response = await API.post("/money-pool-blocks", apiData);
  return response.data;
};

const updateBlockedFund = async (
  blockId: number,
  blockData: Partial<Omit<BlockedFundType, "block_id" | "money_pool_id">>
): Promise<BlockedFundType> => {
  const response = await API.put(`/money-pool-blocks/${blockId}`, blockData);
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
  updateMoneyPool,
};
