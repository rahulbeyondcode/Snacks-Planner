import * as yup from "yup";

export const blockFundsFormSchema = yup.object({
  name: yup.string().required("Name is required"),
  date: yup.string().required("Date is required"),
  amount: yup
    .string()
    .required("Amount is required")
    .test("maxAmount", (value, context) => {
      const { availablePoolAmount } = context.options.context || {};
      if (Number(value) <= Number(availablePoolAmount || 0)) {
        return true;
      }
      return context.createError({
        message: `Amount must be less than or equal to available pool amount (Rs. ${availablePoolAmount ?? 0})`,
      });
    })
    .test("minAmount", "Amount must be greater than 0", (value) => {
      return Number(value) > 0;
    }),
});

export const blockFundsFormDefaultValues = {
  name: "",
  date: "",
  amount: "",
};
