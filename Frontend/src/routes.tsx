import { Navigate, Route, Routes } from "react-router-dom";

import Dashboard from "features/dashboard";
import Layout from "shared/components/layout";

export default function AppRoutes() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Navigate to="dashboard" replace />} />
        <Route path="dashboard" element={<Dashboard />} />
        <Route path="money-pool" element={<div>Money Pool Page</div>} />
        <Route path="assign-roles" element={<div>Assign Roles Page</div>} />
        <Route path="manage" element={<div>Manage Page</div>} />
        <Route path="notifications" element={<div>Notifications Page</div>} />
      </Route>
    </Routes>
  );
}
