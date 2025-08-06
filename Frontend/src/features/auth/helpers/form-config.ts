import * as yup from "yup";

// Login form validation schema
export const loginSchema = yup.object({
  email: yup
    .string()
    .required("Email is required")
    .email("Please enter a valid email address"),
  password: yup.string().required("Password is required"),
});

// Login form default values
export const loginDefaultValues = {
  email: "",
  password: "",
};
