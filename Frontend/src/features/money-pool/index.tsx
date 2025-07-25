import AccountsMoneyPoolView from "features/money-pool/components/accounts-money-pool-view";
import SnackManagerMoneyPoolView from "features/money-pool/components/snack-manager-money-pool-view";

import { useAuthStore } from "features/auth/store";

const MoneyPoolManagement = () => {
  const { hasAnyOfTheseRoles } = useAuthStore();

  if (hasAnyOfTheseRoles(["accounts"])) {
    return <AccountsMoneyPoolView />;
  }
  if (hasAnyOfTheseRoles(["snack-manager"])) {
    return <SnackManagerMoneyPoolView />;
  }
  // No access for other roles
  return (
    <div className="text-center text-red-500 mt-10 text-xl font-bold">
      You do not have access to this feature.
    </div>
  );
};

export default MoneyPoolManagement;
