import { Route, Routes } from "react-router-dom";
import Dashboard from "./features/dashboard";

export default function AppRoutes() {
  return (
    <Routes>
      <Route path="/" element={<Dashboard />} />
      <Route
        path="/user-contribution"
        element={<div>User Contribution Page</div>}
      />
      <Route path="/money-pool" element={<div>Money Pool Page</div>} />
      <Route path="/assign-roles" element={<div>Assign Roles Page</div>} />
      <Route path="/manage" element={<div>Manage Page</div>} />
    </Routes>
  );
}
