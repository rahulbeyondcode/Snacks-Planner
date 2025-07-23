import { Outlet } from "react-router-dom";

const MainContent = () => {
  return (
    <section className="w-full h-full min-h-0 p-4 overflow-auto">
      <Outlet />
    </section>
  );
};

export default MainContent;
