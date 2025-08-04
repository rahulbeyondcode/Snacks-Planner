import { Navigate, Route, Routes } from "react-router-dom";

import Dashboard from "features/dashboard";
import MoneyPool from "features/money-pool";
import Notifications from "features/notifications";
import RoleManagement from "features/role-management";
import UserContribution from "features/user-contribution";
import EmployeeDirectory from "features/employee-directory";
import Layout from "shared/components/layout";

export default function AppRoutes() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Navigate to="dashboard" replace />} />
        <Route path="dashboard" element={<Dashboard />} />
        <Route path="money-pool" element={<MoneyPool />} />
        <Route path="employee-directory" element={<EmployeeDirectory />} />
        <Route path="assign-roles" element={<RoleManagement />} />
        <Route path="user-contribution" element={<UserContribution />} />
        <Route path="manage" element={<UserContribution />} />
        <Route path="notifications" element={<Notifications />} />
      </Route>
    </Routes>
  );
}
