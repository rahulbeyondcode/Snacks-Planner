import * as yup from "yup";

// Yup schema for validation
export const groupSchema = yup.object({
  id: yup.string().required(),
  name: yup.string().required("Group name is required"),
  memberIds: yup
    .array()
    .of(yup.string().required())
    .min(1, "At least one member required")
    .default([])
    .required(),
  snackManagerIds: yup
    .array()
    .of(yup.string().required())
    .min(1, "At least one snack manager required")
    .max(4, "Max 4 snack managers allowed")
    .default([])
    .required()
    .test(
      "snack-managers-in-members",
      "Snack managers must be selected from group members.",
      (snackManagerIds: string[] = [], ctx: yup.TestContext) =>
        (snackManagerIds || []).every((id) =>
          (ctx.parent.memberIds || []).includes(id)
        )
    ),
});

export const schema = yup.object({
  groups: yup
    .array()
    .of(groupSchema)
    .test(
      "unique-group-names",
      "Group names must be unique.",
      (groups: { name: string }[] = []) => {
        const names = groups.map((g) => g.name?.trim().toLowerCase() ?? "");
        return names.length === new Set(names).size;
      }
    )
    .test(
      "unique-employees",
      "Each employee can only be in one group.",
      (groups: { memberIds?: string[] }[] = []) => {
        const allIds = groups.flatMap((g) => g.memberIds ?? []);
        return allIds.length === new Set(allIds).size;
      }
    ),
});
