# WorkTrack HRIS
### Employee Management & Geolocation-Based Attendance Platform

WorkTrack HRIS is a web-based Human Resource Information System designed to simplify employee administration, organizational management, attendance tracking, and leave management.

Built with **Laravel 12, Blade, Tailwind CSS v4, Alpine.js, and MySQL**, WorkTrack provides a centralized platform for managing workforce operations with role-based access control, office-aware organizational structures, and GPS-based attendance validation.

> **Project Status:** Active Development  
> **Project Category:** Full-Stack Web Application · HRIS · Workforce Management

---

## 01. Project Overview

Managing employee information, attendance, and organizational structures across multiple office locations can become increasingly complex when handled manually.

WorkTrack HRIS aims to address these challenges through a centralized workforce management platform that supports essential HR operations.

The application currently provides three operational roles:

- **Admin:** System and organizational administration.
- **HR:** Employee administration, attendance monitoring, leave approval, and reporting.
- **Employee:** Personal attendance, leave requests, and profile management.

The project is currently evolving from a single-company application toward a multi-company SaaS architecture.

Its primary engineering focus is building maintainable business logic, reliable data validation, and secure organizational access boundaries.

## 02. Key Features

- **Role-Based Access Control:** Separate dashboards and application permissions.
- **Employee Management:** Employee records, organizational assignments, and employee status management.
- **Geolocation Attendance:** GPS-based attendance validation using the Haversine formula.
- **Office-Aware Organization:** Office-scoped departments and flexible position structures.
- **Office-Based Scheduling:** Configurable check-in and check-out times for individual offices.
- **Leave Management:** Employee leave and permit requests with HR approval workflows.
- **Attendance Notifications:** Notifications for relevant leave request status updates.
- **Attendance Reporting:** Monthly attendance summaries with Excel and CSV exports.
- **Data Validation:** Scoped uniqueness validation and server-side business rule enforcement.

## 03. Core Modules

### Authentication & Access Control

- Session-based authentication.
- Login and logout.
- Role-based middleware.
- Protected application routes.
- Role-specific dashboards.
- Profile management.
- Password updates.

| Role | Responsibilities |
|---|---|
| Admin | Organizational administration, office management, and master data |
| HR | Employee management, attendance monitoring, leave approval, and reporting |
| Employee | Personal attendance, leave requests, and profile management |

### Organizational Management

**Department Management**
- Department CRUD operations.
- Department search and pagination.
- Office assignment.
- Office-scoped department records.
- Duplicate department name validation within the same office.

**Position Management**

WorkTrack supports two position scopes:

| Position Type | Description |
|---|---|
| Global | Shared organizational position across offices |
| Office-Specific | Position belonging to a particular office |

Position name uniqueness follows the corresponding scope:

- Global position names must be globally unique.
- Office-specific position names must be unique within the same office.
- Different offices may use the same office-specific position name.
- Global and office-specific positions may share the same name.

Departments and positions are connected through a many-to-many relationship using the `departement_position` pivot table.

**Employee Management**
- Employee CRUD operations.
- Employee search and filtering.
- Department filtering.
- Employee status management.
- Avatar upload.
- Organizational assignment.

**Office Management**
- Office CRUD operations.
- Office coordinate configuration.
- Attendance radius configuration.
- Office-specific check-in and check-out schedules.

### Attendance Management

- Employee check-in and check-out.
- Browser-based GPS detection.
- Office geofence validation.
- Attendance history.
- Late attendance detection.
- Office schedule validation.
- Attendance filtering and monitoring.
- GPS accuracy and distance information.

### Leave & Permit Management

- Employee leave and permit requests.
- Leave request history.
- HR approval and rejection.
- Leave status tracking.
- Employee notifications when requests are approved.
- Separate leave and permit management workflows.

### Attendance Reporting

- Monthly attendance summaries.
- Date filtering.
- Department filtering.
- Attendance status filtering.
- Excel (`.xlsx`) export.
- CSV export.

## 04. Geolocation-Based Attendance

One of WorkTrack's core technical features is GPS-based attendance validation.

The application uses the browser Geolocation API to obtain employee coordinates and compares them with registered office coordinates.

### Attendance Validation Flow

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

### Attendance Validation Rules

- GPS permission handling.
- Latitude and longitude detection.
- GPS accuracy recording.
- Geographic distance calculation.
- Office radius validation.
- Attendance location recording.
- Nearest office identification.
- Duplicate daily check-in prevention.

### Office-Based Work Schedule

Each office can define its own attendance schedule.

Current scheduling behavior:

- Configurable check-in time.
- Configurable check-out time.
- Five-minute lateness tolerance.
- Late-minute calculation based on the original scheduled check-in time.
- Check-out availability based on the configured office schedule.

## 05. Technology Stack

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
| Map Integration | Leaflet |
| Testing | PHPUnit |
| Code Formatting | Laravel Pint |
| Version Control | Git & GitHub |

### Engineering Practices

- MVC architecture.
- Form Request validation.
- Role-based authorization.
- Eloquent model relationships.
- Service-oriented business logic.
- Database migrations and seeders.
- Scoped uniqueness validation.
- Feature testing.
- Server-side validation.
- Relational database integrity.

## 06. Database Architecture

The current application uses a relational database structure.

### Main Application Tables

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
      Department Position Employees
          |        |
          +---+----+
              |
      Department-Position
            Pivot
```

The current organizational model supports:

- Departments assigned to offices.
- Global positions.
- Office-specific positions.
- Many-to-many department-position relationships.
- Employee organizational assignments.
- Office references in attendance records.

### Current Architecture Status

WorkTrack currently operates as a **single-tenant application**.

The database has not yet completed the company-level data isolation implementation.

A transition toward a Shared Database + Shared Schema multi-tenant architecture is planned.

## 07. Multi-Tenant Architecture Roadmap

The next major architectural milestone is transforming WorkTrack into a multi-company HRIS SaaS platform.

The following decisions have been established for the planned architecture.

| Component | Architectural Decision |
|---|---|
| Tenant Model | Company-based tenancy |
| Database Strategy | Shared Database + Shared Schema |
| Company Registration | Self-service / Hybrid |
| Company Owner | Automatically assigned as Owner membership |
| Initial Trial | 14 days |
| Platform Administration | Separate Platform Admin |
| Company Lifecycle | Trial, Active, Inactive, and Suspended management |
| HR Access | Configurable branch/office-level access |
| Data Isolation | Company-level server-side isolation |

### Planned Company Lifecycle

1. A user registers independently.
2. The user creates a company.
3. The registering user receives Owner membership.
4. The new company receives a 14-day trial.
5. Platform Admin can verify, activate, suspend, and manage companies.
6. When the trial expires, operational access is restricted until activation.
7. Company data is retained rather than automatically deleted.

### Planned Access Structure

- **Platform Admin:** Platform-wide company administration.
- **Owner:** Full access to all branches and offices within their company.
- **HR Manager:** Broad or assigned-branch access depending on configured permissions.
- **HR Staff:** Restricted access to assigned branches or offices.
- **Employee:** Access to personal workforce information and permitted operations.

> **Important:** These are established architectural decisions for future implementation. They should not be interpreted as completed application features.

## 08. Project Structure

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

## 09. Installation & Setup

### Prerequisites

- PHP 8.2 or newer.
- Composer.
- Node.js and npm.
- MySQL.
- Required PHP extensions for Laravel.

### Clone Repository

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

### Run Migrations and Seeders

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

### Start Development Server

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

## 10. Testing & Code Quality

WorkTrack includes automated feature tests covering application workflows, authorization, validation, and business rules.

Run the test suite:

```bash
php artisan test --compact
```

Format PHP code:

```bash
vendor/bin/pint --dirty
```

Check code differences:

```bash
git diff --check
```

Inspect registered routes:

```bash
php artisan route:list
```

Check migration status:

```bash
php artisan migrate:status
```

Test coverage and passing test counts evolve throughout development. Refer to the latest test execution for the current results.

## 11. Development Roadmap

| Area | Planned Development |
|---|---|
| Multi-Tenancy | Company-level data isolation |
| Company Management | Company registration and lifecycle management |
| Platform Administration | Separate Platform Admin dashboard |
| Access Control | Company and branch-level permissions |
| Workforce Management | Advanced shift management |
| Leave Management | Leave balance and entitlement management |
| Payroll | Payroll processing |
| Attendance | Overtime management |
| Compliance | Audit logs and activity tracking |
| Analytics | Advanced HR dashboards |
| Integration | API integration |
| Mobile | Mobile application |

These items represent planned development and are not necessarily available in the current application.

## 12. Engineering Focus

WorkTrack is developed with an emphasis on practical software engineering and maintainable application architecture.

Key engineering considerations include:

- Designing relational data structures for organizational hierarchies.
- Implementing role-based authorization boundaries.
- Building reusable validation rules.
- Maintaining data integrity through application and database constraints.
- Developing geolocation-based business logic.
- Testing application behavior through automated feature tests.
- Preparing the existing application for future multi-tenant architecture.

## 13. Author

**Muhammad Syafi'i**

Software Developer · Laravel & Backend Development

- GitHub: [@MasMuham24](https://github.com/MasMuham24)
- Repository: [WorkTrack-HRIS](https://github.com/MasMuham24/WorkTrack-HRIS)

---

## Project Status

**Active Development**

WorkTrack HRIS is an ongoing software engineering project focused on building a maintainable, secure, and extensible workforce management platform.

The current development phase focuses on strengthening organizational data management and preparing the application for its planned multi-company SaaS architecture.
