# 🍽️ SnackPlanner - Office Snacks Management System

**SnackPlanner** is an open-source web app designed to streamline snack distribution in office environments. Employees contribute money, the company contributes a multiplier amount, and rotating teams manage snacks weekly. Built for privacy-focused companies who want local control over their operational tools.

---

## 📚 Table of Contents

* [Project Overview](#project-overview)
* [Features](#features)
* [Role System](#role-system)
* [Tech Stack](#tech-stack)
* [Project Setup](#project-setup)
* [Pages Overview](#pages-overview)
* [Future Roadmap](#future-roadmap)
* [License](#license)

---

## 🌟 Project Overview

SnackPlanner digitizes office snack distribution with a simple role-based web app. It replaces paper sheets and Excel tracking, offering real-time tracking, receipts upload, money visualization, and employee management.

---

## ✨ Features

* Role-based system for Accounts, Team Managers, Operations, and Employees.
* Employee payment tracking.
* Money pool calculation (employee contributions + company multiplier).
* Snack planning per day (supports multiple items).
* Veg/Non-Veg categorization.
* Delivery charge, discount, shop tracking.
* Upload snack receipts.
* Flexible fund blocking for special days/events.
* Profit/Loss visualization to aid planning.
* Data exports (PDF/XLS) for accounts team.
* Masterlists for Snacks and Shops.
* Holiday management.
* Fully internal use with local data control.

---

## 👮‍ Role System

| Role             | Permissions                                                                                                                                                |
| ---------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Accounts**     | Add/remove employees, assign team managers, manage masterlists, manage holidays, download reports. Cannot handle daily snack operations.                   |
| **Team Manager** | Monthly admins. Manage snack planning, payment tracking, holidays, fund blocking, assign weekly operations team. Operate within their assigned month only. |
| **Operations**   | Weekly subgroup. Handle snack planning, marking payments, receipt uploads, and tracking for their week. Limited to their week only.                        |
| **Employees**    | Future features: view menus, payment status, suggest snacks, rate snacks.                                                                                  |

---

## 🛠️ Tech Stack

* React JS (Frontend)
* Tailwind CSS (UI Styling)
* Redux (State Management)
* Axios (API Calls)
* Node.js / Express.js (Backend)
* MongoDB / Local JSON Storage

---

## 📚 Project Setup

```bash
# Clone Repository
git clone https://github.com/rahulbeyondcode/SnackPlanner.git

# Install Dependencies
cd SnackPlanner
npm install

# Start Development Server
npm start
```

---

## 🔢 Pages Overview

* **Page 1: Employee Contributions**

  * List employees.
  * Mark Paid / Unpaid.
  * Search functionality.

* **Page 2: Money Pool Setup**

  * Input amount per person.
  * Select company multiplier.
  * Real-time pool calculation.

* **Page 3: Snack Planning**

  * Add multiple snacks per day.
  * Receipt uploads.
  * Delivery, discount tracking.
  * Shop dropdown (add new shops).
  * Profit/Loss tracking.

---

## 📊 Future Roadmap

* Employee-facing features (suggest snacks, rate snacks).
* Auto-access rotation.
* Analytics: Snack frequency, cost trends.
* Mobile-friendly UI.
* Multi-language support.
* Self-hosting guides.

---

## 📚 License

SnackPlanner is released under the [MIT License](LICENSE). Free to use, modify, and distribute.

---

## 📢 Contributing

Contributions are welcome! Please raise issues, request features, or submit pull requests via GitHub.

Happy snacking! 🍽️
