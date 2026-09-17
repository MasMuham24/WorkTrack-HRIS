# Employee Attendance & HRIS

A modern **Human Resource Information System (HRIS)** and employee attendance management platform built with **Laravel 12**, **Blade**, **Tailwind CSS**, and **MySQL**.

The system is designed to help organizations manage employee information, departments, positions, attendance, leave requests, office locations, and **location-based attendance validation** through a secure role-based access control system.

---

## 🚀 Project Overview

**e-Absensi HRIS** is a web-based HR management application focused on digitizing employee administration and attendance workflows.

The system supports three user roles:

* **Admin** — Full system and master-data management
* **HR** — Employee, attendance, leave, and reporting management
* **Employee** — Personal attendance, leave requests, and profile management

A key feature of this project is the **geolocation-based attendance system**, which validates an employee's location against a configured office radius using the **Haversine formula**.

---

## 📌 Current Release

**Version:** `v1.1.0`

### What's New in v1.1.0

* Office Management
* Office Location Configuration
* Configurable Attendance Radius
* Browser Geolocation API
* Haversine Distance Calculation
* Geolocation Radius Validation
* GPS Accuracy Recording
* Latitude & Longitude Recording
* Google Maps Location Integration
* Improved Attendance Validation

---

# ✨ Core Features

## 🔐 Authentication & Authorization

* Manual authentication without Laravel Breeze
* Login & Logout
* Session-based authentication
* Role-Based Access Control (RBAC)
* Protected routes using middleware
* Role-specific dashboards and features

### User Roles

| Role     | Access                      |
| -------- | --------------------------- |
| Admin    | Full system management      |
| HR       | HR & employee management    |
| Employee | Personal attendance & leave |

---

# 👨‍💼 Admin Features

### Dashboard

* Employee statistics
* Attendance statistics
* Leave request statistics
* HR overview

### Department Management

* Create department
* Edit department
* Delete department
* Search department
* Pagination

### Position Management

* Create position
* Edit position
* Delete position
* Search position
* Pagination

### Employee Management

* Create employee
* Edit employee
* Delete employee
* Employee search
* Department filtering
* Employee status management
* Avatar upload
* Pagination

### Office Management

* Create office
* Edit office
* Delete office
* Search office
* Configure office coordinates
* Configure attendance radius

> Office location and radius configuration are restricted to **Admin**.

---

# 👨‍💼 HR Features

### HR Dashboard

* Employee overview
* Attendance statistics
* Leave request overview

### Employee Management

* View employees
* Search employees
* Filter by department
* Manage employee information
* Manage positions and departments

### Attendance Management

* View attendance records
* Search attendance
* Filter by date
* Filter by department
* Filter by attendance status
* View attendance history
* View employee location
* View GPS accuracy
* View distance from office
* View attendance location status
* Open attendance coordinates in Google Maps

### Leave Management

* View leave requests
* Review leave requests
* Approve leave requests
* Reject leave requests
* Manage leave status

### Attendance Reports

* Attendance summary
* Date filtering
* Department filtering
* Attendance statistics

### HR Personal Attendance

HR users can also perform their own:

* Check In
* Check Out
* Attendance history
* Geolocation validation

---

# 👨‍💻 Employee Features

## Dashboard

* Personal dashboard
* Attendance summary
* Leave request summary

## Attendance

* Check In
* Check Out
* Daily check-in validation
* Daily check-out validation
* Late attendance detection
* Attendance history

## 📍 Geolocation Attendance

The attendance system uses the browser's **Geolocation API** to validate the employee's physical location.

### Features

* GPS permission request
* Automatic latitude detection
* Automatic longitude detection
* GPS accuracy detection
* Distance calculation
* Office radius validation
* Attendance location recording
* Google Maps coordinate integration

### Attendance Validation Flow

```text
Employee
   │
   ▼
Login
   │
   ▼
Attendance Check In
   │
   ▼
Request Browser GPS
   │
   ▼
Get Latitude / Longitude
   │
   ▼
Calculate Distance
   │
   ▼
Haversine Formula
   │
   ▼
Compare With Office Radius
   │
   ├───────────────┐
   │               │
   ▼               ▼
Inside Radius   Outside Radius
   │               │
   ▼               ▼
Attendance       Attendance
Recorded         Rejected
```

---

# 📝 Leave Management

Employees can submit:

* Vacation leave
* Sick leave
* Other leave requests

Employees can also:

* View submitted requests
* Check request status
* View leave history

### Leave Workflow

```text
Employee
   │
   ▼
Submit Leave Request
   │
   ▼
Pending
   │
   ├───────────────┐
   │               │
   ▼               ▼
Approved         Rejected
```

---

# 📊 Attendance Management

Attendance records contain important information such as:

* Employee
* Office
* Attendance date
* Check-in time
* Check-out time
* Attendance status
* Late minutes
* Latitude
* Longitude
* GPS accuracy
* Distance from office
* Notes

This allows HR and Admin users to review attendance history and verify whether an attendance record was submitted from an authorized office location.

---

# 🏢 Office Management

Each office can have a configured:

* Office name
* Latitude
* Longitude
* Attendance radius

Example:

```text
Office
 ├── Name
 ├── Latitude
 ├── Longitude
 └── Radius
```

The office coordinates act as the reference point for geolocation attendance validation.

---

# 🧮 Haversine Distance Calculation

The system uses the **Haversine formula** to calculate the distance between:

```text
Employee GPS Location
        │
        ▼
Latitude + Longitude
        │
        ▼
Office Coordinates
        │
        ▼
Haversine Calculation
        │
        ▼
Distance in Meters
```

The calculated distance is then compared with the configured office attendance radius.

---

# 📈 Reporting

The system provides attendance reporting with:

* Date filtering
* Department filtering
* Attendance status filtering
* Attendance summary
* Attendance statistics

This allows HR and Admin users to monitor attendance patterns and generate useful operational information.

---

# 🛠 Tech Stack

### Backend

* **Laravel 12**
* **PHP 8.2+**
* **Eloquent ORM**

### Frontend

* **Blade**
* **Tailwind CSS**
* **JavaScript**

### Database

* **MySQL**

### Browser API

* **Geolocation API**

### Architecture

* MVC Architecture
* Role-Based Access Control
* RESTful Resource Controllers
* Form Request Validation
* Eloquent Relationships

---

# 📂 Project Structure

```text
e-Absensi/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   ├── Employee/
│   │   │   ├── HR/
│   │   │   └── Report/
│   │   │
│   │   ├── Middleware/
│   │   └── Requests/
│   │
│   ├── Models/
│   └── Services/
│
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│
├── resources/
│   └── views/
│       ├── admin/
│       ├── employee/
│       ├── hr/
│       ├── layouts/
│       └── reports/
│
├── routes/
│   ├── web.php
│   ├── auth.php
│   ├── profile.php
│   ├── admin.php
│   ├── hr.php
│   ├── employee.php
│   ├── management.php
│   └── reports.php
│
├── public/
├── storage/
├── tests/
├── .env.example
├── artisan
├── composer.json
└── README.md
```

---

# 🗄 Database

### Main Tables

```text
users
departemens
positions
offices
attendances
leave_requests
```

### Main Relationships

```text
User
 │
 ├── Department
 ├── Position
 ├── Attendances
 └── Leave Requests

Department
 │
 ├── Users
 └── Positions

Office
 │
 └── Attendances
```

---

# 🔑 Role-Based Access

The application separates access based on user roles.

```text
                    ┌──────────────┐
                    │     USER     │
                    └──────┬───────┘
                           │
             ┌─────────────┼─────────────┐
             │             │             │
             ▼             ▼             ▼
          ADMIN            HR         EMPLOYEE
             │             │             │
             ▼             ▼             ▼
       Full Access     HR Access    Personal Access
```

Backend authorization is enforced using role middleware to prevent unauthorized access even when users manually access protected URLs.

---

# ⚙️ Installation

## 1. Clone Repository

```bash
git clone https://github.com/MasMuham24/e-absensi.git
```

## 2. Enter Project Directory

```bash
cd e-absensi
```

## 3. Install PHP Dependencies

```bash
composer install
```

## 4. Create Environment File

```bash
cp .env.example .env
```

For Windows PowerShell, you can use:

```powershell
Copy-Item .env.example .env
```

## 5. Generate Application Key

```bash
php artisan key:generate
```

## 6. Configure Database

Update the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_absensi
DB_USERNAME=root
DB_PASSWORD=
```

## 7. Run Migration & Seeder

```bash
php artisan migrate --seed
```

## 8. Create Storage Link

```bash
php artisan storage:link
```

## 9. Start Development Server

```bash
php artisan serve
```

Application:

```text
http://127.0.0.1:8000
```

---

# 🧪 Development

Clear Laravel caches when necessary:

```bash
php artisan optimize:clear
```

Check registered routes:

```bash
php artisan route:list
```

Run tests:

```bash
php artisan test
```

---

# 🔄 Business Workflow

```text
                    LOGIN
                      │
                      ▼
                Role Validation
                      │
        ┌─────────────┼─────────────┐
        │             │             │
        ▼             ▼             ▼
      ADMIN           HR         EMPLOYEE
        │             │             │
        │             │             ▼
        │             │         Attendance
        │             │             │
        │             ▼             ▼
        │         HR Management   Leave Request
        │             │             │
        └──────┬──────┴─────────────┘
               │
               ▼
        Attendance & Leave
             Reports
```

---

# 🎯 Learning Objectives

This project demonstrates practical implementation of:

* Laravel MVC architecture
* Manual authentication
* Role-Based Access Control
* Middleware authorization
* CRUD operations
* Eloquent ORM
* Model relationships
* Form Request validation
* Search & filtering
* Pagination
* File upload
* Attendance business logic
* Leave management workflow
* Office management
* Browser Geolocation API
* Haversine distance calculation
* Radius-based validation
* GPS accuracy handling
* Reporting system
* Modular route architecture
* Service-based business logic

---

# 📝 Changelog

## v1.1.0 — Office & Geolocation

### Added

* Office Management
* Office Location Configuration
* Attendance Radius Configuration
* Browser Geolocation Integration
* Haversine Distance Calculation
* Radius Validation
* GPS Accuracy Recording
* Latitude & Longitude Recording
* Google Maps Coordinate Integration

### Improved

* Attendance validation
* Attendance workflow
* Database structure
* Role-based access control
* Management route separation

---

## v1.0.0 — Initial Release

### Added

* Authentication
* Role-Based Access Control
* Employee Management
* Department Management
* Position Management
* Attendance Management
* Leave Management
* Attendance Reporting

---

# 🔮 Future Development

Planned improvements may include:

* Payroll Management
* Overtime Management
* Shift Management
* Work Schedule Management
* Holiday Calendar
* Employee Performance Management
* Notification System
* Export Attendance Reports
* Advanced Analytics Dashboard
* Audit Logs
* API Integration
* Mobile Application

---

# 📄 License

This project is intended for **educational purposes and portfolio demonstration**.

---

# 👤 Author

**Muhammad Syafi'i**

GitHub: [MasMuham24](https://github.com/MasMuham24)

---

## ⭐ Project Status

**Active Development**

This project is continuously being improved with new HR management features, attendance capabilities, security improvements, and production-oriented architecture.
