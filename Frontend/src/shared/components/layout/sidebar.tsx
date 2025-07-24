import { useAuthStore } from "features/auth/store";
import {
  HiOutlineBell,
  HiOutlineCog,
  HiOutlineCurrencyDollar,
  HiOutlineUserGroup,
  HiOutlineViewGrid,
} from "react-icons/hi";
import { Link, useLocation } from "react-router-dom";

const navItems = [
  { label: "Dashboard", path: "/dashboard", icon: HiOutlineViewGrid },
  { label: "Money Pool", path: "/money-pool", icon: HiOutlineCurrencyDollar },
  { label: "Assign Roles", path: "/assign-roles", icon: HiOutlineUserGroup },
  { label: "Manage", path: "/manage", icon: HiOutlineCog },
  { label: "Notifications", path: "/notifications", icon: HiOutlineBell },
];

const Sidebar = () => {
  const location = useLocation();
  const { user } = useAuthStore();

  const userNameSplit = user?.name?.split(" ");

  return (
    <div className="w-full h-full flex flex-col items-center">
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
  );
};

export default Sidebar;
