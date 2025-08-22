import { create } from "zustand";

export type Employee = {
  user_id: number;
  name: string;
  email: string;
};

type EmployeeDirectoryStore = {
  isModalOpen: boolean;
  modalMode: "add" | "edit";
  selectedEmployee: Employee | null;
  openCreateModal: () => void;
  openEditModal: (employee: Employee) => void;
  closeModal: () => void;
};

export const useEmployeeDirectoryStore = create<EmployeeDirectoryStore>(
  (set) => ({
    isModalOpen: false,
    modalMode: "add",
    selectedEmployee: null,

    openCreateModal: () =>
      set({
        isModalOpen: true,
        modalMode: "add",
        selectedEmployee: null,
      }),

    openEditModal: (employee: Employee) =>
      set({
        isModalOpen: true,
        modalMode: "edit",
        selectedEmployee: employee,
      }),

    closeModal: () =>
      set({
        isModalOpen: false,
        selectedEmployee: null,
      }),
  })
);
