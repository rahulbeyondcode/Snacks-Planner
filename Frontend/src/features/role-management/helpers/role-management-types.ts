// Flat employee list type
export type Employee = { id: string; name: string; email: string };

// Group type
export type Group = {
  id: string;
  name: string;
  memberIds: string[];
  snackManagerIds: string[];
};
