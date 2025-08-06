import { useAuthStore } from "features/auth/store";
import {
  Bell,
  DollarSign,
  Grid3X3,
  LogOut,
  Settings,
  UserPlus,
  Users,
} from "lucide-react";
import { Link, useLocation, useNavigate } from "react-router-dom";

const navItems = [
  { label: "Dashboard", path: "/dashboard", icon: Grid3X3 },
  { label: "User Contribution", path: "/user-contribution", icon: UserPlus },
  { label: "Money Pool", path: "/money-pool", icon: DollarSign },
  {
    label: "Employee Directory",
    path: "/employee-directory",
    icon: Users,
  },
  { label: "Assign Roles", path: "/assign-roles", icon: Users },
  { label: "Manage", path: "/manage", icon: Settings },
  { label: "Notifications", path: "/notifications", icon: Bell },
];

const Sidebar = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const { user } = useAuthStore();

  const userNameSplit = user?.name?.split(" ");

  const handleLogout = () => {
    navigate("/login", { replace: true });
  };

  return (
    <div className="w-full h-full flex flex-col items-center justify-between">
      <div className="w-full flex flex-col items-center">
        {/* Avatar and name */}
        <div className="flex flex-col items-center mb-8">
          <div className="w-14 h-14 rounded-full bg-blue-200 flex items-center justify-center text-xl font-bold text-blue-900 mb-2 shadow-lg">
            {(userNameSplit?.[0]?.[0] || "") + (userNameSplit?.[1]?.[0] || "")}
          </div>
          <div className="text-white text-base font-semibold tracking-wide">
            {user?.name || ""}
          </div>
        </div>
        {/* Nav list */}
        <nav className="flex flex-col gap-2 w-full">
          {navItems.map((item) => {
            const Icon = item.icon;
            const isActive = location.pathname === item.path;
            return (
              <Link
                key={item.label}
                to={item.path}
                className={`cursor-pointer flex items-center gap-3 px-5 py-3 focus:outline-none w-[95%] rounded-tr-3xl rounded-br-3xl
                ${isActive ? "bg-indigo-400/30 text-white font-medium" : "text-blue-100 hover:bg-indigo-200/20 hover:text-white"}
              `}
              >
                <span
                  className={`flex items-center justify-center w-7 h-7 rounded-lg ${isActive ? "bg-indigo-300 text-indigo-900" : "bg-indigo-900 text-indigo-200 group-hover:bg-indigo-200 group-hover:text-indigo-900"}`}
                >
                  <Icon size={20} />
                </span>
                <span className="tracking-tight font-sans text-base">
                  {item.label}
                </span>
              </Link>
            );
          })}
        </nav>
      </div>

      {/* Logout button */}
      <div className="w-full px-2 pb-4">
        <button
          onClick={handleLogout}
          className="cursor-pointer flex items-center gap-3 px-5 py-3 focus:outline-none w-full rounded-3xl text-blue-100 hover:bg-red-400/20 hover:text-white transition-colors"
        >
          <span className="flex items-center justify-center w-7 h-7 rounded-lg bg-red-500 text-white">
            <LogOut size={20} />
          </span>
          <span className="tracking-tight font-sans text-base">Logout</span>
        </button>
      </div>
    </div>
  );
};

export default Sidebar;
