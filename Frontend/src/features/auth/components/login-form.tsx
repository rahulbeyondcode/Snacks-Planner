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
            className="w-full flex items-center justify-center rounded-lg py-3 text-sm font-semibold text-yellow-50 bg-black focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-black transition shadow-[4px_4px_0_0_#000]"
          >
            <span>Sign in</span>
          </button>
        </form>
      </FormProvider>
    </div>
  );
};
