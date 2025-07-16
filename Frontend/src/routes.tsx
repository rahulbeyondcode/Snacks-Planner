import { Route, Routes, Navigate } from "react-router-dom";
import EmployeeContributions from "./pages/EmployeeContributions";
import MoneyPoolSetup from "./pages/MoneyPoolSetup";

export default function AppRoutes() {
  return (
    <Routes>
      <Route path="/" element={<Navigate to="/contributions" replace />} />
      <Route path="/contributions" element={<EmployeeContributions />} />
      <Route path="/money-pool" element={<MoneyPoolSetup />} />
    </Routes>
  );
}
