import type { User } from "features/auth/types/auth-types";

export interface LoginCredentials {
  email: string;
  password: string;
}

// Simple hardcoded users for development (no API needed)
const validUsers: (User & { password: string })[] = [
  {
    id: "1",
    name: "John Snow",
    email: "john.doe@company.com",
    role: "snack-manager",
    password: "password123",
  },
  {
    id: "2",
    name: "Jane Smith",
    email: "jane.smith@company.com",
    role: "accounts",
    password: "password123",
  },
  {
    id: "3",
    name: "Mike Johnson",
    email: "mike.johnson@company.com",
    role: "operations",
    password: "password123",
  },
];

// Simple authentication functions (no API calls)
export const authService = {
  validateCredentials(credentials: LoginCredentials): User | null {
    const user = validUsers.find(
      (u) =>
        u.email === credentials.email && u.password === credentials.password
    );

    if (!user) {
      return null;
    }

    // Remove password from response
    const { password: _, ...userWithoutPassword } = user;
    return userWithoutPassword;
  },
};
