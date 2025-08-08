import { yupResolver } from "@hookform/resolvers/yup";
import React from "react";
import { FormProvider, useForm } from "react-hook-form";
import { useNavigate } from "react-router-dom";

import InputField from "shared/components/form-components/input-field";

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

  const onSubmit = (formData: LoginFormData) => {
    console.log(formData);
    navigate("/dashboard");
  };

  return (
    <div className="w-full max-w-md mx-auto">
      <FormProvider {...methods}>
        <form onSubmit={methods.handleSubmit(onSubmit)} className="space-y-6">
          <InputField
            name="email"
            label="Email Address"
            type="email"
            placeholder="Enter your email"
            required
          />

          <InputField
            name="password"
            label="Password"
            type="password"
            placeholder="Enter your password"
            required
          />

          <button
            type="submit"
            className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Sign in
          </button>
        </form>
      </FormProvider>
    </div>
  );
};
