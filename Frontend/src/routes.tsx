import { Navigate, Route, Routes } from "react-router-dom";

import { LoginPage } from "features/auth";
import Dashboard from "features/dashboard";
import EmployeeDirectory from "features/employee-directory";
import MoneyPool from "features/money-pool";
import Notifications from "features/notifications";
import RoleManagement from "features/role-management";
import UserContribution from "features/user-contribution";
import Layout from "shared/components/layout";
import { ProtectedRoute } from "shared/components/protected-route";
import { PublicRoute } from "shared/components/public-route";

export default function AppRoutes() {
  return (
    <Routes>
      {/* Public Routes */}
      <Route
        path="/login"
        element={
          <PublicRoute>
            <LoginPage />
          </PublicRoute>
        }
      />

      {/* Protected Routes */}
      <Route
        path="/"
        element={
          <ProtectedRoute>
            <Layout />
          </ProtectedRoute>
        }
      >
        <Route index element={<Navigate to="dashboard" replace />} />
        <Route path="dashboard" element={<Dashboard />} />
        <Route path="money-pool" element={<MoneyPool />} />
        <Route path="employee-directory" element={<EmployeeDirectory />} />
        <Route path="assign-roles" element={<RoleManagement />} />
        <Route path="user-contribution" element={<UserContribution />} />
        <Route path="manage" element={<UserContribution />} />
        <Route path="notifications" element={<Notifications />} />
      </Route>

      {/* Catch all route - redirect to dashboard if authenticated, login if not */}
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
}
