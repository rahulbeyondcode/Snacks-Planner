import MainContent from "shared/components/layout/main-content";
import Sidebar from "shared/components/layout/sidebar";

const Layout = () => {
  return (
    <div className="w-screen min-h-screen bg-yellow-50 text-black">
      <div className="min-h-screen w-screen flex gap-4 p-4">
        <aside className="h-[calc(100vh-32px)] w-80 sm:w-96 flex flex-col py-6 rounded-2xl border-2 border-black bg-white shadow-[8px_8px_0_0_#000]">
          <Sidebar />
        </aside>
        <main className="bg-white w-full rounded-2xl border-2 border-black shadow-[8px_8px_0_0_#000] p-4 sm:p-6 h-[calc(100vh-32px)] overflow-hidden flex flex-col min-h-0">
          <MainContent />
        </main>
      </div>
    </div>
  );
};

export default Layout;
