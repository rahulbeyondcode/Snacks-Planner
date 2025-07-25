import { create } from "zustand";

import type { AuthState, User } from "features/auth/types/auth-types";
import type { UserRole } from "shared/helpers/types";

export const useAuthStore = create<AuthState>((set, get) => ({
  user: {
    id: "1",
    name: "John Snow",
    email: "john.doe@company.com",
    role: "snack-manager",
  },
  isAuthenticated: true,

  setUser: (user: User) => set({ user, isAuthenticated: true }),

  logout: () => set({ user: null, isAuthenticated: false }),

  hasAnyOfTheseRoles: (roles: UserRole[]) => {
    const { user } = get();
    return user ? roles.includes(user.role) : false;
  },
}));
