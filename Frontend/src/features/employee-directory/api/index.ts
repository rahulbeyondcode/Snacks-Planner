import type { Employee } from "features/employee-directory";
import API from "shared/helpers/api";

const getEmployees = async () => {
  const response = await API.get("/employees");
  return response.data;
};

const addEmployee = async (employee: Omit<Employee, "id">) => {
  const response = await API.post("/employees", employee);
  return response.data;
};

const updateEmployee = async (employee: Employee) => {
  const response = await API.put(`/employees/${employee.id}`, employee);
  return response.data;
};

const deleteEmployee = async (id: number) => {
  const response = await API.delete(`/employees/${id}`);
  return response.data;
};

export { addEmployee, deleteEmployee, getEmployees, updateEmployee };
