import AccountsMoneyPoolView from "features/money-pool/components/accounts-money-pool-view";
import SnackManagerMoneyPoolView from "features/money-pool/components/snack-manager-money-pool-view";

import type { UserRoleType } from "features/money-pool/types/money-pool-types";

// TODO: Replace this with actual user role from auth/context
const mockUserRole: UserRoleType = "accounts"; // Change to 'snack-manager' to test

const MoneyPoolManagement = () => {
  if (mockUserRole === "accounts") {
    return <AccountsMoneyPoolView />;
  }
  if (mockUserRole === "snack-manager") {
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
