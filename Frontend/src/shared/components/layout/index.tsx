import MainContent from "shared/components/layout/main-content";
import Sidebar from "shared/components/layout/sidebar";

const Layout = () => {
  return (
    <div className="w-screen h-screen">
      <div className="w-screen h-screen flex">
        <aside className="h-screen w-96 flex flex-col items-center py-8 shadow-xl">
          <Sidebar />
        </aside>
        <main className="bg-white w-full rounded-[35px] shadow-xl m-4 p-8 flex items-center justify-center">
          <MainContent />
        </main>
      </div>
    </div>
  );
};

export default Layout;
