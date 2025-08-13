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
    <div className="w-full h-full flex flex-col justify-between">
      <div className="w-full">
        {/* Avatar and name */}
        <div className="flex flex-col items-center mb-8">
          <div className="w-14 h-14 rounded-full bg-yellow-300 text-black border-2 border-black flex items-center justify-center text-xl font-extrabold mb-2 shadow-[4px_4px_0_0_#000]">
            {(userNameSplit?.[0]?.[0] || "") + (userNameSplit?.[1]?.[0] || "")}
          </div>
          <div className="text-black text-base font-semibold tracking-wide">
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
                className={`group cursor-pointer flex items-center gap-3 px-4 py-3 focus:outline-none w-[95%] mx-auto rounded-xl border-2 transition-all ${isActive ? "bg-black text-yellow-50 border-black shadow-[4px_4px_0_0_#000]" : "bg-white text-black border-black hover:bg-yellow-100"}`}
              >
                <span
                  className={`flex items-center justify-center w-7 h-7 rounded-lg border ${isActive ? "bg-yellow-400 text-black border-black" : "bg-black text-yellow-50 border-black group-hover:bg-black group-hover:text-yellow-50"}`}
                >
                  <Icon size={20} />
                </span>
                <span className="font-sans text-sm sm:text-base">
                  {item.label}
                </span>
              </Link>
            );
          })}
        </nav>
      </div>

      {/* Logout button */}
      <div className="w-full px-2 pb-4">
        <div className="w-[95%] mx-auto mb-2 border-dashed border-black/10" />
        <button
          onClick={handleLogout}
          className="cursor-pointer flex items-center gap-2 px-3 py-2 focus:outline-none w-[95%] mx-auto rounded-lg border border-black/20 bg-transparent text-black hover:bg-yellow-100/60 transition"
        >
          <span className="flex items-center justify-center w-6 h-6 rounded-md bg-yellow-300/60 text-black border border-black/20">
            <LogOut size={16} />
          </span>
          <span className="font-sans text-sm sm:text-base font-medium">
            Logout
          </span>
        </button>
      </div>
    </div>
  );
};

export default Sidebar;
