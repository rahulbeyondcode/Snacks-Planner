import * as yup from "yup";

// Category Form Configuration
export const categorySchema = yup.object({
  name: yup.string().required("Category name is required"),
});

export const categoryFormDefaultValues = {
  name: "",
};

// Shop Form Configuration
export const shopSchema = yup.object({
  name: yup.string().required("Shop name is required"),
  address: yup.string().required("Address is required"),
  contactDetails: yup.string().required("Contact Number is required"),
  paymentMode: yup.string().required("Payment mode is required"),
  notes: yup.string().optional().default(""),
});

export const shopFormDefaultValues = {
  name: "",
  address: "",
  contactDetails: "",
  paymentMode: "",
  notes: "",
};

// Snack Form Configuration
export const snackSchema = yup.object({
  name: yup.string().required("Snack name is required"),
  categoryId: yup.string().required("Category is required"),
  price: yup
    .number()
    .positive("Price must be positive")
    .required("Price is required"),
  notes: yup.string().optional().default(""),
});

export const snackFormDefaultValues = {
  name: "",
  categoryId: "",
  price: 0,
  notes: "",
};

// No Snack Day Form Configuration
export const noSnackDaySchema = yup.object({
  holidayName: yup.string().required("Holiday name is required"),
  date: yup.date().required("Date is required"),
});

export const noSnackDayFormDefaultValues = {
  holidayName: "",
  date: new Date(),
};
