import AccountsMoneyPoolView from "features/money-pool/components/accounts-money-pool-view";
import SnackManagerMoneyPoolView from "features/money-pool/components/snack-manager-money-pool-view";

const MoneyPoolManagement = () => {
  return <AccountsMoneyPoolView />;
  return <SnackManagerMoneyPoolView />;

  // if (hasAnyOfTheseRoles(["accounts"])) {
  //   return <AccountsMoneyPoolView />;
  // }
  // if (hasAnyOfTheseRoles(["snack-manager"])) {
  //   return <SnackManagerMoneyPoolView />;
  // }
  // No access for other roles
  return (
    <div className="w-full flex items-center justify-center mt-10 px-4">
      <div className="max-w-xl w-full bg-white border-2 border-black rounded-2xl shadow-[8px_8px_0_0_#000] p-6 sm:p-8 text-center">
        <div className="inline-block bg-yellow-300 border-2 border-black rounded-lg px-3 py-1 mb-4 text-sm font-semibold text-black">
          Access Restricted
        </div>
        <p className="text-base sm:text-lg font-semibold text-red-600">
          You do not have access to this feature.
        </p>
        <p className="text-sm text-black/70 mt-2">
          Please contact your administrator if you believe this is a mistake.
        </p>
      </div>
    </div>
  );
};

export default MoneyPoolManagement;
