import type { Employee } from "features/employee-directory/store";
import API from "shared/helpers/api";

const getEmployees = async () => {
  const response = await API.get("/users");
  return response.data.data;
};

const addEmployee = async (employee: Omit<Employee, "id">) => {
  const response = await API.post("/users", employee);
  return response.data.data;
};

const updateEmployee = async (employee: Employee) => {
  const response = await API.put(`/users/${employee.id}`, employee);
  return response.data.data;
};

const deleteEmployee = async (id: number) => {
  const response = await API.delete(`/users/${id}`);
  return response.data.data;
};

export { addEmployee, deleteEmployee, getEmployees, updateEmployee };
