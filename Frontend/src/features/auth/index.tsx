import { LoginForm } from "features/auth/components/login-form";

const LoginPage = () => {
  return (
    <div className="min-h-screen relative overflow-hidden bg-yellow-50 text-black">
      <header className="px-6 sm:px-8 pt-6 flex items-center justify-between">
        <div className="inline-flex items-center gap-3">
          <div className="h-9 w-9 grid place-items-center rounded-lg bg-black text-yellow-50 text-lg">
            🍪
          </div>
          <span className="text-xl font-extrabold tracking-tight">
            SnackPlanner
          </span>
        </div>
      </header>

      <div className="relative flex min-h-[calc(100vh-64px)] flex-col justify-center py-10 sm:px-6 lg:px-8">
        <div className="sm:mx-auto sm:w-full sm:max-w-md">
          <div className="bg-white border-2 border-black rounded-2xl px-6 py-8 sm:px-10 shadow-[6px_6px_0_0_#000]">
            <div className="sm:mx-auto sm:w-full sm:max-w-md mb-8 text-center">
              <h1 className="mt-1 text-3xl sm:text-4xl font-extrabold tracking-tight">
                Welcome back
              </h1>
              <p className="mt-2 text-sm text-black/70">
                Sign in to fuel your snack journey
              </p>
            </div>

            <LoginForm />

            <div className="mt-8">
              <div className="relative">
                <div className="absolute inset-0 flex items-center">
                  <div className="w-full border-t border-black/10" />
                </div>
                <div className="relative flex justify-center text-sm">
                  <span className="px-2 bg-white text-black/70">
                    Need help?
                  </span>
                </div>
              </div>

              <div className="mt-6 text-center">
                <p className="text-sm text-black/70">
                  Contact your administrator if you're having trouble accessing
                  your account.
                </p>
              </div>
            </div>
          </div>
        </div>

        <div className="mt-10 text-center">
          <p className="text-xs text-black/60">
            &copy; 2025 SnackPlanner. All rights reserved.
          </p>
        </div>
      </div>
    </div>
  );
};

export default LoginPage;
