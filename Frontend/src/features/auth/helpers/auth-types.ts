import * as yup from "yup";

import type { loginSchema } from "features/auth/helpers/form-config";
import type { UserRole } from "shared/helpers/types";

export type User = {
  id: string;
  name: string;
  email: string;
  role: UserRole;
};

export type AuthState = {
  user: User | null;
  isAuthenticated: boolean;
  setUser: (user: User) => void;
  logout: () => void;
  hasAnyOfTheseRoles: (roles: UserRole[]) => boolean;
};

// Login form type
export type LoginFormData = yup.InferType<typeof loginSchema>;
