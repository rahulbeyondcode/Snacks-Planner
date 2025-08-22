import type {
  MoneyPoolFormType,
  MoneyPoolType,
} from "features/money-pool/helpers/money-pool-types";
import API from "shared/helpers/api";

const getMoneyPool = async (): Promise<MoneyPoolType> => {
  const response = await API.get("/money-pools");
  return response.data;
};

const updateMoneyPool = async (
  formData: MoneyPoolFormType
): Promise<MoneyPoolType> => {
  // Transform form data to API structure
  const apiData = {
    amount_per_person: formData.amountCollectedPerPerson,
    company_contribution_multiplier: formData.companyContributionMultiplier,
    // Calculate derived values
    total_amount_collected:
      (formData.totalEmployees || 0) * formData.amountCollectedPerPerson,
    company_contribution:
      (formData.totalEmployees || 0) *
      formData.amountCollectedPerPerson *
      formData.companyContributionMultiplier,
    number_of_paid_people: formData.totalEmployees || 0,
  };

  const response = await API.put("/money_pool", apiData);
  return response.data;
};

export { getMoneyPool, updateMoneyPool };
