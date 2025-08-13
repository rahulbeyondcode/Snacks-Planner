export type MoneyPoolType = {
  amountCollectedPerPerson: number;
  companyContributionMultiplier: number; // e.g. 2 for 2x
  paidEmployees: number;
  totalEmployees?: number;
  totalCollectedFromEmployees: number;
  companyContribution: number;
  finalPoolAmount: number;
};

export type BlockedFundType = {
  id: string;
  name: string;
  date: string; // ISO string
  amount: string;
};
