import { Outlet } from "react-router-dom";

const MainContent = () => {
  return (
    <section className="w-full h-full min-h-0 overflow-y-auto p-2 sm:p-6">
      <Outlet />
    </section>
  );
};

export default MainContent;
