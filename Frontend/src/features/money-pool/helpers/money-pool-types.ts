export type MoneyPoolType = {
  amount_per_person: number;
  total_amount_collected: number;
  company_contribution: number;
  company_contribution_multiplier: number;
  number_of_paid_people: number;
  blocked_funds?: BlockedFundType[];
};

// For frontend form handling - maps to API structure
export type MoneyPoolFormType = {
  amountCollectedPerPerson: number;
  companyContributionMultiplier: number;
  totalEmployees?: number;
};

export type BlockedFundType = {
  id: string;
  name: string;
  date: string; // ISO string
  amount: string;
};
