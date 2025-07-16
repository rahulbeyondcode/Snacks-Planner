import { createSlice } from "@reduxjs/toolkit";

/**
 * @typedef {Object} Employee
 * @property {number} id - Unique employee ID
 * @property {string} name - Employee name
 * @property {string} email - Employee email address
 * @property {boolean} paid - Has the employee paid?
 */

interface Employee {
  id: number;
  name: string;
  email: string;
  paid: boolean;
}

interface EmployeeState {
  employees: Employee[];
}

const initialState: EmployeeState = {
  employees: [
    { id: 1, name: "Abdul Ansheer M S", email: "ansheer@quintetsolutions.com", paid: false },
    { id: 2, name: "Abhijith R", email: "abhijith@quintetsolutions.com", paid: false },
    { id: 3, name: "Abhira V", email: "abhira@quintetsolutions.com", paid: false },
    { id: 4, name: "Abiraj P R", email: "abiraj@quintetsolutions.com", paid: false },
    { id: 5, name: "Ajai Mathew Jose", email: "ajaimathew@quintetsolutions.com", paid: false },
    { id: 6, name: "Ajay Krishna", email: "ajay.krishna@quintetsolutions.com", paid: false },
    { id: 7, name: "Ajaygopal Jayaprakash", email: "ajaygopal@quintetsolutions.com", paid: false },
    { id: 8, name: "Akhil Dileep", email: "akhildileep@quintetsolutions.com", paid: false },
    { id: 9, name: "Alex K J", email: "alex@quintetsolutions.com", paid: false },
    { id: 10, name: "Alfin Jiji", email: "alfin@quintetsolutions.com", paid: false },
    { id: 11, name: "Amal K K", email: "amal@quintetsolutions.com", paid: false },
    { id: 12, name: "Amal Pradeep", email: "amal.p@quintetsolutions.com", paid: false },
    { id: 13, name: "Amal T S", email: "amal.ts@quintetsolutions.com", paid: false },
    { id: 14, name: "Anandu K Das", email: "anandu@quintetsolutions.com", paid: false },
    { id: 15, name: "Anju Gopi", email: "anju.gopi@quintetsolutions.com", paid: false },
    { id: 16, name: "Anto Stanly", email: "antostanly@quintetsolutions.com", paid: false },
    { id: 17, name: "Archana A", email: "archana.a@quintetsolutions.com", paid: false },
    { id: 18, name: "Arjun R Pillai", email: "arjun@quintetsolutions.com", paid: false },
    { id: 19, name: "Aswanth S", email: "aswanth@quintetsolutions.com", paid: false },
    { id: 20, name: "Athira S Nair", email: "s.athira@quintetsolutions.com", paid: false },
    { id: 21, name: "Ayana Santhosh", email: "ayana@quintetsolutions.com", paid: false },
    { id: 22, name: "Benjamin Shyam", email: "benjamin@quintetsolutions.com", paid: false },
    { id: 23, name: "Binil Baby", email: "binil@quintetsolutions.com", paid: false },
    { id: 24, name: "Binish George", email: "binish@quintetsolutions.com", paid: false },
    { id: 25, name: "Chippy Surendran", email: "chippy@quintetsolutions.com", paid: false },
    { id: 26, name: "Ciril James", email: "ciril@quintetsolutions.com", paid: false },
    { id: 27, name: "Deepak T S", email: "deepak@quintetsolutions.com", paid: false },
    { id: 28, name: "Devajith P", email: "devajith@quintetsolutions.com", paid: false },
    { id: 29, name: "Dimple Dominic", email: "dimple@quintetsolutions.com", paid: false },
    { id: 30, name: "Elson V C", email: "elson@quintetsolutions.com", paid: false },
    { id: 31, name: "Grace Maria", email: "grace@quintetsolutions.com", paid: false },
    { id: 32, name: "Hafis Safwan", email: "hafis@quintetsolutions.com", paid: false },
    { id: 33, name: "Hrithik P", email: "hrithik@quintetsolutions.com", paid: false },
    { id: 34, name: "Jayanth S", email: "jayanth@quintetsolutions.com", paid: false },
    { id: 35, name: "Jayaraj P R", email: "jayaraj@quintetsolutions.com", paid: false },
    { id: 36, name: "Jayesh Jayan", email: "jayesh@quintetsolutions.com", paid: false },
    { id: 37, name: "Jiby George", email: "jiby@quintetsolutions.com", paid: false },
    { id: 38, name: "Jijo S Chirakadavil", email: "jijo.s@quintetsolutions.com", paid: false },
    { id: 39, name: "Jijomon K A", email: "jijo@quintetsolutions.com", paid: false },
    { id: 40, name: "Jim M", email: "jim@quintetsolutions.com", paid: false },
    { id: 41, name: "Jins Sebastian", email: "jins@quintetsolutions.com", paid: false },
    { id: 42, name: "Jithin Syam N S", email: "jithin@quintetsolutions.com", paid: false },
    { id: 43, name: "Kiran T", email: "kiran@quintetsolutions.com", paid: false },
    { id: 44, name: "Mohammed Rifas", email: "rifas@quintetsolutions.com", paid: false },
    { id: 45, name: "Mrithula Ancy Arthur", email: "mrithula@quintetsolutions.com", paid: false },
    { id: 46, name: "Muhammed Ajmal", email: "ajmal@quintetsolutions.com", paid: false },
    { id: 47, name: "Muhammed Niyas V", email: "niyas@quintetsolutions.com", paid: false },
    { id: 48, name: "Nabeel V", email: "nabeel@quintetsolutions.com", paid: false },
    { id: 49, name: "Najma Muhammed", email: "najma@quintetsolutions.com", paid: false },
    { id: 50, name: "Naveen Ambookken", email: "naveen@quintetsolutions.com", paid: false },
    { id: 51, name: "Nibin Kurian", email: "nibin@quintetsolutions.com", paid: false },
    { id: 52, name: "Nidhin C K", email: "nidhin@quintetsolutions.com", paid: false },
    { id: 53, name: "Nijesh Mannuel", email: "nijesh.m@quintetsolutions.com", paid: false },
    { id: 54, name: "Nikesh P", email: "nikesh@quintetsolutions.com", paid: false },
    { id: 55, name: "Nikila Prasad", email: "nikila@quintetsolutions.com", paid: false },
    { id: 56, name: "Nishad Muhammad", email: "nishad.m@quintetsolutions.com", paid: false },
    { id: 57, name: "Nishanth K V", email: "nishanth@quintetsolutions.com", paid: false },
    { id: 58, name: "Noel Jose", email: "noel@quintetsolutions.com", paid: false },
    { id: 59, name: "Parvathy Krishnan", email: "parvathy@quintetsolutions.com", paid: false },
    { id: 60, name: "Prasheeth C S", email: "prasheeth@quintetsolutions.com", paid: false },
    { id: 61, name: "Praveen P", email: "praveen@quintetsolutions.com", paid: false },
    { id: 62, name: "Rahul R", email: "rahul@quintetsolutions.com", paid: false },
    { id: 63, name: "Raj Mohan", email: "rajmohan@quintetsolutions.com", paid: false },
    { id: 64, name: "Ramsiya P B", email: "ramsiya@quintetsolutions.com", paid: false },
    { id: 65, name: "Renjith C R", email: "renjith@quintetsolutions.com", paid: false },
    { id: 66, name: "Renjith Narayanan", email: "renjith.n@quintetsolutions.com", paid: false },
    { id: 67, name: "Rex Jacob", email: "rex@quintetsolutions.com", paid: false },
    { id: 68, name: "Rithin Dinesh", email: "rithin@quintetsolutions.com", paid: false },
    { id: 69, name: "Rohith S", email: "rohith@quintetsolutions.com", paid: false },
    { id: 70, name: "Roniya K J", email: "roniya@quintetsolutions.com", paid: false },
    { id: 71, name: "Sandeep E M", email: "sandeep@quintetsolutions.com", paid: false },
    { id: 72, name: "Sanjay J", email: "sanjay.j@quintetsolutions.com", paid: false },
    { id: 73, name: "Sarika K V", email: "sarika@quintetsolutions.com", paid: false },
    { id: 74, name: "Sebin Francis", email: "sebin@quintetsolutions.com", paid: false },
    { id: 75, name: "Shahanaz B", email: "shahanaz@quintetsolutions.com", paid: false },
    { id: 76, name: "Shamil Erkara", email: "shamil@quintetsolutions.com", paid: false },
    { id: 77, name: "Shanoj K S", email: "shanoj@quintetsolutions.com", paid: false },
    { id: 78, name: "Shine Sunil", email: "shine@quintetsolutions.com", paid: false },
    { id: 79, name: "Sidharth A", email: "sidharth@quintetsolutions.com", paid: false },
    { id: 80, name: "Sojo C J", email: "sojo@quintetsolutions.com", paid: false },
    { id: 81, name: "Sreekuttan P S", email: "sreekuttan@quintetsolutions.com", paid: false },
    { id: 82, name: "Sreenath E S", email: "sreenath@quintetsolutions.com", paid: false },
    { id: 83, name: "Sreenath T K", email: "sreenath.tk@quintetsolutions.com", paid: false },
    { id: 84, name: "Swathy Sreenivasan", email: "swathy@quintetsolutions.com", paid: false },
    { id: 85, name: "Thajul Mushthaq", email: "thajul@quintetsolutions.com", paid: false },
    { id: 86, name: "Vijoy Varghese", email: "vijoy@quintetsolutions.com", paid: false },
    { id: 87, name: "Vineeth C K", email: "vineeth@quintetsolutions.com", paid: false },
    { id: 88, name: "Visakh K", email: "visakh@quintetsolutions.com", paid: false },
    { id: 89, name: "Vishnu V Sali", email: "vishnu@quintetsolutions.com", paid: false },
    { id: 90, name: "Vysakh Radhakrishnan", email: "vysakh@quintetsolutions.com", paid: false }
  ]
};

const employeeSlice = createSlice({
  name: "employees",
  initialState,
  reducers: {
    togglePaid(state, action) {
      const emp = state.employees.find((e) => e.id === action.payload);
      if (emp) emp.paid = !emp.paid;
    },
  },
});

export const { togglePaid } = employeeSlice.actions;
export default employeeSlice.reducer;
