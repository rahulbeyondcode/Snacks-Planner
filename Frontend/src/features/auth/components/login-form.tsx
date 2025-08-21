import { yupResolver } from "@hookform/resolvers/yup";
import { useMutation } from "@tanstack/react-query";
import React from "react";
import { FormProvider, useForm } from "react-hook-form";
import { useNavigate } from "react-router-dom";

import InputField from "shared/components/form-components/input-field";

import { login } from "features/auth/api";
import type { LoginFormData } from "features/auth/helpers/auth-types";
import {
  loginDefaultValues,
  loginSchema,
} from "features/auth/helpers/form-config";

export const LoginForm: React.FC = () => {
  const navigate = useNavigate();

  const methods = useForm<LoginFormData>({
    resolver: yupResolver(loginSchema),
    defaultValues: loginDefaultValues,
  });

  const loginMutation = useMutation({
    mutationFn: login,
    onSuccess: (response) => {
      console.log("Login successful:", response);
      // TODO: Store user data in global state/context
      navigate("/dashboard");
    },
    onError: (error) => {
      console.error("Login failed:", error);
    },
  });

  const onSubmit = (formData: LoginFormData) => {
    loginMutation.mutate(formData);
  };

  return (
    <div className="w-full max-w-md mx-auto">
      <FormProvider {...methods}>
        <form onSubmit={methods.handleSubmit(onSubmit)} className="space-y-6">
          {loginMutation.error && (
            <div className="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
              {(loginMutation.error as any)?.response?.data?.message ||
                loginMutation.error?.message ||
                "Login failed. Please try again."}
            </div>
          )}

          <div className="space-y-2">
            <InputField
              name="email"
              label="Email Address"
              type="email"
              placeholder="Enter your email"
              required
            />
          </div>

          <div className="space-y-2">
            <InputField
              name="password"
              label="Password"
              type="password"
              placeholder="Enter your password"
              required
            />
          </div>

          <button
            type="submit"
            disabled={loginMutation.isPending}
            className="w-full rounded-lg py-3 text-sm font-semibold text-yellow-50 bg-black focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-black transition shadow-[4px_4px_0_0_#000] disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span>{loginMutation.isPending ? "Signing in..." : "Sign in"}</span>
          </button>
        </form>
      </FormProvider>
    </div>
  );
};
