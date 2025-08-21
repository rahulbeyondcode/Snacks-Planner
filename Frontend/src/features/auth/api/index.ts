import axios from "axios";
import { toast } from "react-toastify";

import API from "shared/helpers/api";

import type { LoginFormData } from "features/auth/helpers/auth-types";

// Types for API responses
export type LoginResponse = {
  user: {
    id: string;
    name: string;
    email: string;
    role: string;
  };
  token?: string;
};

// Get CSRF cookie before making login request
const getCsrfCookie = async () => {
  const response = await axios.get(
    `${import.meta.env.VITE_API_BACKEND_URL}/sanctum/csrf-cookie`
  );
  return response.data;
};

// Login user with email and password
const login = async (credentials: LoginFormData): Promise<LoginResponse> => {
  try {
    await getCsrfCookie();

    const response = await API.post("/login", credentials);
    return response.data;
  } catch (error) {
    toast.error("Login failed. Please try again.");

    if (error instanceof Error) {
      throw error;
    }

    throw new Error("Login failed. Please try again.");
  }
};

export { getCsrfCookie, login };
