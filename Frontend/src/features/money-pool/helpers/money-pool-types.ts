export type CreatorType = {
  id: number;
  name: string;
  email: string;
};

export type SettingsType = {
  money_pool_setting_id: number;
  per_month_amount: number;
  multiplier: number;
};

export type BlockedFundType = {
  block_id: number;
  money_pool_id: number;
  amount: number;
  reason: string;
  block_date: string;
};

export type MoneyPoolType = {
  money_pool_id: number;
  total_collected_amount: number;
  employer_contribution: number;
  total_pool_amount: number;
  blocked_amount: number;
  total_available_amount: number;
  creator: CreatorType;
  settings: SettingsType;
  blocks: BlockedFundType[];
};

// For frontend form handling - maps to API structure
export type MoneyPoolFormType = {
  amountCollectedPerPerson: number;
  companyContributionMultiplier: string | number;
  totalEmployees?: number;
};

export type BlockedFundPayloadType = Omit<BlockedFundType, "block_id">;

export type MoneyPoolPayloadType = {
  per_month_amount: number;
  multiplier: number;
};
