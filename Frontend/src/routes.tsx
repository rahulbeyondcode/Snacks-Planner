import { Navigate, Route, Routes } from "react-router-dom";

import Dashboard from "features/dashboard";
import MoneyPool from "features/money-pool";
import Notifications from "features/notifications";
import RoleManagement from "features/role-management";
import UserContribution from "features/user-contribution";
import Layout from "shared/components/layout";

export default function AppRoutes() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Navigate to="dashboard" replace />} />
        <Route path="dashboard" element={<Dashboard />} />
        <Route path="money-pool" element={<MoneyPool />} />
        <Route path="assign-roles" element={<RoleManagement />} />
        <Route path="manage" element={<UserContribution />} />
        <Route path="notifications" element={<Notifications />} />
      </Route>
    </Routes>
  );
}
