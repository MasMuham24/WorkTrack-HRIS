# WorkTrack HRIS
### Employee Management & Geolocation-Based Attendance Platform

**WorkTrack HRIS** is a web-based Human Resource Information System designed to streamline employee administration, organizational management, and attendance operations.

Built with Laravel 12, Blade, Tailwind CSS, Alpine.js, and MySQL, the platform combines role-based access control, office-aware organizational structures, and geolocation-based attendance validation into a centralized HR management application.

> **Project Status:** Active Development  
> **Category:** Full-Stack Web Application · HRIS · Workforce Management

---

## 01. Project Overview

Managing employee information and attendance across an organization requires consistent data, reliable validation, and clear access boundaries.

WorkTrack addresses these needs through a centralized HR management platform that supports three operational roles: Admin, HR, and Employee.

The system is designed around practical HR workflows, including employee administration, department and position management, attendance tracking, leave approval, and attendance reporting.

A key engineering focus is the development of an office-aware organizational structure that can serve as the foundation for future multi-company support.

## 02. Key Highlights

- **Role-Based Access Control:** Separate permissions and workflows for Admin, HR, and Employee.
- **Geolocation Attendance:** Validate attendance locations using GPS coordinates and the Haversine formula.
- **Office-Aware Organization:** Associate departments and office-specific positions with their respective offices.
- **Flexible Position Structure:** Support both global and office-specific positions.
- **Configurable Work Schedules:** Define check-in and check-out schedules for each office.
- **Attendance Reporting:** Generate monthly attendance reports and export data to Excel and CSV.
- **Leave Management:** Support employee requests and HR approval workflows.

---

## 03. Core Modules

### Authentication & Access Control

- Session-based authentication.
- Login and logout.
- Role-based middleware.
- Protected application routes.
- Role-specific dashboards.
- Profile management and password updates.

| Role | Primary Responsibilities |
|---|---|
| Admin | System administration, master data, office, and organizational management |
| HR | Employee administration, attendance, leave approval, and reporting |
| Employee | Personal attendance, leave requests, and profile management |

### Organizational Management

WorkTrack provides organizational data management with office-level relationships.

**Department Management**
- Department CRUD operations.
- Department search and pagination.
- Office assignment.
- Office-scoped department records.

**Position Management**

The application supports two position types:

| Type | Description |
|---|---|
| Global | Shared organizational position that can be associated with departments across offices |
| Office-Specific | Position scoped to a particular office |

Position name validation follows the corresponding scope:

- Global position names must be globally unique.
- Office-specific position names must be unique within the same office.
- Different offices may use the same office-specific position name.
- Global and office-specific positions may share the same name.

Departments and positions use a many-to-many relationship through the `departement_position` pivot table.

### Employee Management

- Employee CRUD operations.
- Employee search and filtering.
- Department filtering.
- Employee status management.
- Avatar upload.
- Organizational assignment.

### Office Management

- Office CRUD operations.
- Office coordinate configuration.
- Attendance radius configuration.
- Office-specific check-in and check-out schedules.

### Attendance Management

- Employee check-in and check-out.
- GPS-based location validation.
- Attendance history.
- Late attendance detection.
- Office schedule validation.
- Attendance filtering and monitoring.
- GPS accuracy and distance information.

### Leave Management

- Employee leave and permit requests.
- Leave request history.
- HR approval and rejection.
- Leave status tracking.
- Employee notifications when requests are approved.

### Attendance Reporting

- Monthly attendance summaries.
- Date and department filtering.
- Attendance status filtering.
- Excel (`.xlsx`) export.
- CSV export.

---

## 04. Geolocation-Based Attendance

One of WorkTrack's main technical features is location-based attendance validation.

The application uses the browser Geolocation API to obtain employee coordinates and compares them with the configured office location.

**Validation process**

```text
Employee
   |
   v
Request GPS Permission
   |
   v
Get Latitude & Longitude
   |
   v
Retrieve Office Coordinates
   |
   v
Calculate Geographic Distance
   |
   v
Haversine Formula
   |
   v
Compare Distance With Office Radius
   |
   +-----------------------+
   |                       |
   v                       v
Within Radius         Outside Radius
   |                       |
   v                       v
Attendance Accepted   Attendance Rejected
```

**Attendance validation includes:**

- Browser GPS permission handling.
- Latitude and longitude detection.
- GPS accuracy recording.
- Geographic distance calculation.
- Office radius validation.
- Attendance location recording.
- Google Maps coordinate integration.

### Office-Based Work Schedule

Each office can define its own attendance schedule.

Current scheduling behavior includes:

- Configurable check-in time.
- Configurable check-out time.
- Five-minute lateness tolerance.
- Late-minute calculation based on the scheduled check-in time.

---

## 05. Technical Architecture

WorkTrack follows Laravel's MVC architecture with dedicated Form Requests, Eloquent relationships, middleware, and service-based business logic.

### Technology Stack

| Layer | Technology |
|---|---|
| Backend Framework | Laravel 12 |
| Programming Language | PHP 8.2+ |
| Frontend | Blade |
| CSS Framework | Tailwind CSS v4 |
| JavaScript | Alpine.js, JavaScript |
| Database | MySQL |
| ORM | Eloquent |
| Geolocation | Browser Geolocation API |
| Map Integration | Leaflet and Google Maps |
| Testing | PHPUnit |

### Engineering Practices

- MVC architecture.
- Role-based authorization.
- Form Request validation.
- Eloquent model relationships.
- Service-oriented business logic.
- Database migrations.
- Scoped uniqueness constraints.
- Feature testing.
- Server-side validation.

---

## 06. Database Design

Main application tables:

```text
users
offices
departemens
positions
departement_position
attendances
leave_requests
```

### Organizational Relationships

```text
                 Office
                   |
          +--------+--------+
          |        |        |
          v        v        v
     Department Position  Employees
          |        |
          +---+----+
              |
       Department-Position
             Pivot
```

The organizational model supports:

- Departments assigned to offices.
- Global positions.
- Office-specific positions.
- Many-to-many department-position relationships.
- Employee organizational assignments.
- Office references in attendance records.

The current architecture is a single-tenant application undergoing preparation for future company-level data isolation.

---

## 07. Project Structure

```text
WorkTrack-HRIS/
|
+-- app/
|   +-- Http/
|   |   +-- Controllers/
|   |   +-- Middleware/
|   |   +-- Requests/
|   |
|   +-- Models/
|   +-- Rules/
|   +-- Services/
|
+-- bootstrap/
+-- database/
|   +-- factories/
|   +-- migrations/
|   +-- seeders/
|
+-- resources/
|   +-- views/
|   +-- css/
|   +-- js/
|
+-- routes/
+-- public/
+-- storage/
+-- tests/
|
+-- artisan
+-- composer.json
+-- package.json
+-- README.md
```

---

## 08. Installation & Setup

### Prerequisites

- PHP 8.2 or newer.
- Composer.
- Node.js and npm.
- MySQL.
- PHP extensions required by Laravel.

### Clone the Repository

```bash
git clone https://github.com/MasMuham24/WorkTrack-HRIS.git

cd WorkTrack-HRIS
```

### Install Dependencies

```bash
composer install
npm install
```

### Configure Environment

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

### Configure Database

Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=worktrack
DB_USERNAME=root
DB_PASSWORD=
```

### Run Database Migrations

```bash
php artisan migrate --seed
```

### Create Storage Link

```bash
php artisan storage:link
```

### Build Frontend Assets

```bash
npm run build
```

### Start the Development Server

```bash
php artisan serve
```

Application URL:

```text
http://127.0.0.1:8000
```

For frontend development:

```bash
npm run dev
```

---

## 09. Testing & Code Quality

WorkTrack includes feature tests for important application workflows and business rules.

Run the test suite:

```bash
php artisan test --compact
```

Format PHP code:

```bash
vendor/bin/pint --dirty
```

Inspect registered routes:

```bash
php artisan route:list
```

Check migration status:

```bash
php artisan migrate:status
```

**Latest development test snapshot:** 69 tests passed, with 464 assertions.

---

## 10. Development Roadmap

The project is actively evolving toward a more complete workforce management platform.

| Area | Planned Development |
|---|---|
| Multi-Tenancy | Company-level data isolation |
| Platform Administration | Separate Platform Admin |
| Workforce Management | Shift and work schedule management |
| Leave Management | Leave balance management |
| Payroll | Payroll processing |
| Attendance | Overtime management |
| Compliance | Audit logs |
| Analytics | Advanced HR dashboards |
| Integration | API integration |
| Mobile | Mobile application |

These items represent planned development, not currently completed features.

---

## 11. Engineering Focus

The development of WorkTrack emphasizes practical software engineering concerns:

- Designing relational data structures for organizational hierarchies.
- Implementing authorization boundaries across application roles.
- Building reusable validation rules.
- Maintaining data integrity through database constraints.
- Handling geolocation-based business logic.
- Testing application behavior through feature tests.
- Preparing a single-tenant application for a future multi-tenant architecture.

---

## 12. Author

**Muhammad Syafi'i**

Software Developer · Laravel & Backend Development

- GitHub: [@MasMuham24](https://github.com/MasMuham24)
- Repository: [WorkTrack-HRIS](https://github.com/MasMuham24/WorkTrack-HRIS)

---

## Project Status

**Active Development**

WorkTrack HRIS is an ongoing software engineering project focused on building a maintainable, secure, and extensible HR management platform.
