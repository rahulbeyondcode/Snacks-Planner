export type MoneyPoolType = {
  amountCollectedPerPerson: number;
  companyContributionMultiplier: number; // e.g. 2 for 2x
  paidEmployees: number;
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

export type UserRoleType =
  | "accounts"
  | "snack-manager"
  | "operations"
  | "employee";
