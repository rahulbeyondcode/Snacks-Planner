import { useState, useEffect, useRef } from "react";
import { useAppSelector, useAppDispatch } from "../store/store";
import { togglePaid } from "../store/employeeSlice";
import Tick from "../assets/Tick";
import Cross from "../assets/Cross";

import { useNavigate } from "react-router-dom";

export default function EmployeeContributions() {
  const dispatch = useAppDispatch();
  const employees = useAppSelector((state) => state.employees.employees);
  const [search, setSearch] = useState<string>("");
  const [activeTab, setActiveTab] = useState<"all" | "paid" | "unpaid">("all");
  const [loading, setLoading] = useState<boolean>(false);
  const [filtered, setFiltered] = useState(employees);

  // Loader effect for search and tab
  const prevSearchRef = useRef<string>("");
  const prevTabRef = useRef<string>("all");
  useEffect(() => {
    // Only show loader if search or tab actually changed
    if (prevSearchRef.current !== search || prevTabRef.current !== activeTab) {
      setLoading(true);
      const timeout = setTimeout(() => {
        let filteredEmployees = employees.filter((e) =>
          e.name.toLowerCase().includes(search.toLowerCase())
        );
        if (activeTab === "paid") {
          filteredEmployees = filteredEmployees.filter((e) => e.paid);
        } else if (activeTab === "unpaid") {
          filteredEmployees = filteredEmployees.filter((e) => !e.paid);
        }
        setFiltered(filteredEmployees);
        setLoading(false);
      }, 300);
      prevSearchRef.current = search;
      prevTabRef.current = activeTab;
      return () => clearTimeout(timeout);
    } else {
      // If employees changed (e.g. paid/unpaid), just update filtered instantly
      let filteredEmployees = employees.filter((e) =>
        e.name.toLowerCase().includes(search.toLowerCase())
      );
      if (activeTab === "paid") {
        filteredEmployees = filteredEmployees.filter((e) => e.paid);
      } else if (activeTab === "unpaid") {
        filteredEmployees = filteredEmployees.filter((e) => !e.paid);
      }
      setFiltered(filteredEmployees);
    }
  }, [search, employees, activeTab]);

  const [buttonLoading, setButtonLoading] = useState<string | number | null>(null);
  const total = employees.length;
  const paidCount = employees.filter((e) => e.paid).length;

  // Handler for toggling paid with button loader
  const handleTogglePaid = (id: string | number) => {
    setButtonLoading(id);
    setTimeout(() => {
      dispatch(togglePaid(id));
      setButtonLoading(null);
    }, 300);
  };

  const navigate = useNavigate();

  return (
    <div className="w-full max-w-2xl mx-auto bg-white/80 shadow-xl rounded-3xl p-10 border border-gray-100 flex flex-col h-[90vh] min-h-[600px]">
      <h1 className="text-3xl font-extrabold mb-8 text-center text-gray-900 tracking-tight">Employee Contributions</h1>
      <div className="flex justify-center mb-8">
        <div className="inline-flex rounded-xl bg-gray-100 p-1">
          <button
            type="button"
            className={`px-6 py-2 rounded-lg font-semibold text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blue-300 ${activeTab === "all" ? "bg-white text-blue-600 shadow" : "text-gray-500 hover:text-blue-500"}`}
            onClick={() => setActiveTab("all")}
          >
            All
          </button>
          <button
            type="button"
            className={`px-6 py-2 rounded-lg font-semibold text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-green-300 ${activeTab === "paid" ? "bg-white text-green-600 shadow" : "text-gray-500 hover:text-green-500"}`}
            onClick={() => setActiveTab("paid")}
          >
            Paid
          </button>
          <button
            type="button"
            className={`px-6 py-2 rounded-lg font-semibold text-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-red-300 ${activeTab === "unpaid" ? "bg-white text-red-600 shadow" : "text-gray-500 hover:text-red-500"}`}
            onClick={() => setActiveTab("unpaid")}
          >
            Unpaid
          </button>
        </div>
      </div>
      <input
        className="w-full mb-8 px-5 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
        type="text"
        placeholder="Search employees..."
        value={search}
        onChange={(e) => setSearch(e.target.value)}
      />
      <div className="overflow-x-auto rounded-xl border border-gray-100 flex-1 min-h-0">
        <div className="overflow-y-auto h-[400px] sm:h-[400px] max-h-full">
          <table className="min-w-full bg-white/70 text-sm">
          <thead>
            <tr>
              <th className="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Name</th>
              <th className="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr>
                <td colSpan={3} className="py-12 text-center">
                  <span className="inline-block animate-spin rounded-full h-8 w-8 border-2 border-gray-300 border-t-blue-500"></span>
                </td>
              </tr>
            ) : filtered.length === 0 ? (
              <tr>
                <td colSpan={3} className="text-center py-8 text-gray-400 italic">
                  No employees found.
                </td>
              </tr>
            ) : (
              filtered.map((emp) => (
                <tr key={emp.id} className="border-b last:border-0 hover:bg-gray-100/60 transition">
                  <td className="px-6 py-4 text-gray-900 font-medium whitespace-nowrap">{emp.name}</td>
                  <td className="px-6 py-4">
                    <button
                      onClick={() => handleTogglePaid(emp.id)}
                      className={`flex items-center gap-2 px-5 py-2 rounded-xl text-xs font-bold shadow transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 border-0 
                        ${emp.paid 
                          ? "bg-green-500 text-white hover:bg-green-600 focus:ring-green-300" 
                          : "bg-red-500 text-white hover:bg-red-600 focus:ring-red-300"}
                      `}
                      aria-label={`${emp.paid ? "Unpaid" : "Paid"}`}
                      disabled={buttonLoading === emp.id || loading}
                    >
                      {buttonLoading === emp.id ? (
                        <span className="inline-block animate-spin rounded-full h-4 w-4 border-2 border-white border-t-blue-200"></span>
                      ) : emp.paid 
                        ? (<Tick />)
                        : (<Cross />)}
                      {emp.paid ? "Paid" : "Unpaid"}
                    </button>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
        </div>
      </div>
      <div className="flex flex-col sm:flex-row justify-between items-center mt-10 gap-4">
        <div className="text-gray-500 text-base">
          <span className="mr-4">Total: <span className="font-semibold text-gray-900">{total}</span></span>
          <span>Paid: <span className="font-semibold text-green-700">{paidCount}</span></span>
        </div>
      </div>
      <button
        className="w-full mt-8 bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-8 py-3 rounded-xl shadow-md hover:from-blue-600 hover:to-indigo-600 transition-all font-semibold text-base focus:outline-none focus:ring-2 focus:ring-blue-300"
        onClick={() => navigate('/money-pool')}
      >
        Proceed to Money Pool Setup
      </button>
    </div>
  );
}
