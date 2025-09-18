import { useState } from "react";

import AccountsMoneyPoolView from "features/money-pool/components/accounts-money-pool-view";
import SnackManagerMoneyPoolView from "features/money-pool/components/snack-manager-money-pool-view";

const MoneyPoolManagement = () => {
  // Temporary toggle for development - change this to switch between views
  const [currentView, setCurrentView] = useState<"accounts" | "snack-manager">(
    "accounts"
  );

  return (
    <div className="w-full">
      {/* Development Toggle - Remove when auth is implemented */}
      <div className="bg-yellow-100 border-l-4 border-yellow-500 p-4 mb-6">
        <div className="flex items-center justify-between">
          <div className="flex items-center">
            <div className="text-yellow-700 font-medium">Development Mode</div>
          </div>
          <div className="flex space-x-2">
            <button
              onClick={() => setCurrentView("accounts")}
              className={`px-3 py-1 text-sm font-medium rounded border-2 transition-colors ${
                currentView === "accounts"
                  ? "bg-blue-600 text-white border-blue-600"
                  : "bg-white text-blue-600 border-blue-600 hover:bg-blue-50"
              }`}
            >
              Accounts View
            </button>
            <button
              onClick={() => setCurrentView("snack-manager")}
              className={`px-3 py-1 text-sm font-medium rounded border-2 transition-colors ${
                currentView === "snack-manager"
                  ? "bg-green-600 text-white border-green-600"
                  : "bg-white text-green-600 border-green-600 hover:bg-green-50"
              }`}
            >
              Snack Manager View
            </button>
          </div>
        </div>
      </div>

      {/* Render the selected view */}
      {currentView === "accounts" && <AccountsMoneyPoolView />}
      {currentView === "snack-manager" && <SnackManagerMoneyPoolView />}
    </div>
  );

  // Future implementation with proper auth (commented out for now)
  // if (hasAnyOfTheseRoles(["accounts"])) {
  //   return <AccountsMoneyPoolView />;
  // }
  // if (hasAnyOfTheseRoles(["snack-manager"])) {
  //   return <SnackManagerMoneyPoolView />;
  // }
  // return (
  //   <div className="w-full flex items-center justify-center mt-10 px-4">
  //     <div className="max-w-xl w-full bg-white border-2 border-black rounded-2xl shadow-[8px_8px_0_0_#000] p-6 sm:p-8 text-center">
  //       <div className="inline-block bg-yellow-300 border-2 border-black rounded-lg px-3 py-1 mb-4 text-sm font-semibold text-black">
  //         Access Restricted
  //       </div>
  //       <p className="text-base sm:text-lg font-semibold text-red-600">
  //         You do not have access to this feature.
  //       </p>
  //       <p className="text-sm text-black/70 mt-2">
  //         Please contact your administrator if you believe this is a mistake.
  //       </p>
  //     </div>
  //   </div>
  // );
};

export default MoneyPoolManagement;
