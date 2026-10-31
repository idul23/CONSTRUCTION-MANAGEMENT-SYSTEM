
# Construction Project and Labor Management System

A web-based Construction Project and Labor Management System designed to organize and manage construction projects, workers, skills, tasks, assignments, attendance, materials, equipment, suppliers, expenses, and reports.

The system provides a centralized platform for construction-related operations through a role-based management interface and a PostgreSQL relational database.

Built using Node.js, Express.js, PostgreSQL, Sequelize, and EJS, the application focuses on improving construction project coordination, workforce management, resource monitoring, and database organization.

---

## Project Information

| Information | Details |
|---|---|
| Project Name | Construction Project and Labor Management System |
| Project Abbreviation | Construction MS |
| Course | Advanced Database Systems |
| Project Category | ERP / Construction Project Management |
| Backend Framework | Node.js and Express.js |
| Database Management System | PostgreSQL |
| ORM | Sequelize |
| Frontend Rendering | EJS |
| Version Control | Git and GitHub |
| Deployment Platform | Render |

---

## Project Links

| Resource | URL |
|---|---|
| Live Website | https://construction-management-system-5ybe.onrender.com |
| GitHub Repository | https://github.com/idul23/CONSTRUCTION-MANAGEMENT-SYSTEM |
| Local Development | http://localhost:3000 |

### Live Application

**Access Construction MS here:**

https://construction-management-system-5ybe.onrender.com

The application is deployed as a Render Web Service. Its production database is configured through Render environment variables.

The local development address is available when running the application on a local computer.

---

## Table of Contents

1. Project Overview
2. Project Objectives
3. Technologies Used
4. System Features and Modules
5. User Roles and Access Control
6. System Architecture
7. Database Design
8. Database Relationships
9. Database Automation
10. Security
11. Project Structure
12. Installation and Setup
13. Running the Application
14. Production Deployment
15. Git and GitHub Workflow
16. Development Status
17. Future Improvements
18. Contributors

---

## 1. Project Overview

The Construction Project and Labor Management System is a web-based application developed to simplify the management of construction operations.

Construction projects require coordination between project managers, workers, construction materials, equipment, suppliers, tasks, and financial resources.

Managing these records separately can result in duplicated information, difficulty tracking project activities, and inefficient resource management.

Construction MS addresses these challenges by providing a centralized system where authorized users can manage construction-related information and monitor project activities.

The application also applies database engineering concepts such as relational database design, normalization, referential integrity, database automation, transactions, and query optimization.

---

## 2. Project Objectives

The primary objectives of the system are:

- Centralize construction project information.
- Manage worker records and skill information.
- Organize project members and task assignments.
- Monitor project phases and task progress.
- Record worker attendance and working hours.
- Manage construction materials and material consumption.
- Monitor construction equipment and equipment utilization.
- Maintain supplier information.
- Record project-related expenses.
- Generate reports for project monitoring.
- Implement authentication and role-based access control.
- Maintain data consistency through relational database constraints.
- Apply database automation for selected operations.
- Improve database reliability and application performance.

---

## 3. Technologies Used

| Technology | Purpose |
|---|---|
| Node.js | JavaScript runtime environment |
| Express.js | Backend web application framework |
| PostgreSQL | Relational database management system |
| Sequelize | Object-relational mapping (ORM) |
| EJS | Server-side template rendering |
| HTML5 | Web page structure |
| CSS3 | Interface styling and responsiveness |
| JavaScript | Application functionality |
| JWT | User authentication |
| bcryptjs | Password hashing |
| dotenv | Environment variable management |
| cookie-parser | Cookie handling |
| Nodemon | Development server utility |
| Git | Version control |
| GitHub | Source code repository |
| Render | Web application and PostgreSQL hosting |

---

## 4. System Features and Modules

Construction MS contains the following management modules:

### 4.1 Dashboard

Provides an overview of important construction management records and system activities.

### 4.2 Authentication and Login

- User login.
- Authentication using JSON Web Tokens.
- Protected application routes.
- Role-based access restrictions.

### 4.3 Clients Management

Maintains client information associated with construction projects.

### 4.4 Projects Management

- Manage construction project records.
- Maintain project details.
- Record project locations and schedules.
- Monitor project status and budgets.
- Associate projects with clients and project managers.

### 4.5 Workers Management

- Maintain worker information.
- Manage workforce records.
- Organize worker information for construction operations.

### 4.6 Worker Skills

- Maintain available worker skills.
- Associate workers with their corresponding skills.
- Organize workforce capabilities.

### 4.7 Project Members

Manages the association between workers and construction projects.

### 4.8 User Profiles

Maintains additional information associated with system user accounts.

### 4.9 Tasks Management

- Create and manage construction tasks.
- Maintain task details.
- Monitor task status and progress.

### 4.10 Project Phases

Organizes construction projects into different phases and maintains phase-related information.

### 4.11 Task Assignments

- Assign workers to construction tasks.
- Maintain assignment records.
- Associate workers with their assigned tasks.

### 4.12 Attendance Management

- Record worker attendance.
- Maintain attendance records.
- Monitor recorded working hours.

### 4.13 Materials Management

- Manage construction materials.
- Maintain material descriptions and unit costs.
- Monitor available material quantities.
- Maintain reorder information.

### 4.14 Material Usage

- Record construction material consumption.
- Associate material usage with tasks.
- Maintain usage quantities and cost information.
- Record the user responsible for usage entries.

### 4.15 Suppliers Management

Maintains supplier information and supplier-related records.

### 4.16 Equipment Management

- Manage construction equipment records.
- Maintain equipment types and serial numbers.
- Record equipment daily rates.
- Monitor equipment condition.

### 4.17 Equipment Usage

Maintains records of construction equipment utilization.

### 4.18 Expenses Management

- Record project-related expenses.
- Maintain expense descriptions and amounts.
- Associate expenses with construction projects.
- Apply role-based access restrictions.

### 4.19 Reports

Provides access to reports and construction-related information for monitoring and review.

### 4.20 Users Management

Manages system accounts and assigned user roles.

### 4.21 Audit Logs

Maintains records of selected system activities to support accountability and monitoring.

---

## 5. User Roles and Access Control

The application implements three main user roles.

| User Role | Responsibilities |
|---|---|
| Administrator | Manages users, workers, projects, materials, equipment, and administrative records. |
| Project Manager | Manages projects, assignments, progress, and project-related expenses. |
| Site Staff | Handles authorized construction site activities, attendance, working hours, material usage, and task updates. |

### Role-Based Access Control

Access to protected application features is controlled through authentication and authorization middleware.

The backend checks the authenticated user's assigned role before allowing access to restricted routes and operations.

The intended access policy is to ensure that users can only perform operations authorized for their assigned responsibilities.

---

## 6. System Architecture

The application uses a structured Express.js architecture with separated responsibilities for routing, controllers, models, middleware, and views.

### Request and Response Flow

The general application request flow is:

1. The user interacts with the EJS interface.
2. The request is received by the Express.js application.
3. Middleware checks authentication and authorization where required.
4. The appropriate route directs the request to its controller.
5. The controller processes the requested operation.
6. Sequelize models communicate with PostgreSQL.
7. The database returns the requested result.
8. The application sends a response or renders the appropriate EJS page.

### Main Architectural Components

| Component | Responsibility |
|---|---|
| Views | Display the application's user interface using EJS. |
| Routes | Define application endpoints. |
| Controllers | Handle requests and application operations. |
| Middleware | Handle authentication and authorization. |
| Models | Define database entities and relationships. |
| Configuration | Manage application and database configuration. |
| Services | Support reusable application logic where implemented. |

The application separates its presentation, routing, business logic, and database interaction responsibilities.

---

## 7. Database Design

The system uses PostgreSQL as its relational database management system and Sequelize as its ORM.

### Database Information

| Property | Value |
|---|---|
| Database Management System | PostgreSQL |
| ORM | Sequelize |
| Local Development Database | construction_management |
| Production Hosting | Render PostgreSQL |
| Production Database | construction_management_66c7 |

The production database is hosted separately from the local development database.

### Main Database Entities

The system manages information related to:

- Users
- Roles
- User profiles
- Clients
- Projects
- Workers
- Skills
- Worker skills
- Project members
- Project phases
- Tasks
- Task assignments
- Attendance
- Materials
- Material usage
- Suppliers
- Equipment
- Equipment usage
- Expenses
- Audit logs

The final database table count and exact column definitions should be based on the executed SQL schema.

### Normalization

The database is organized into related entities to reduce unnecessary data duplication and improve data consistency.

The database design is intended to follow Third Normal Form (3NF).

Normalization validation should check:

- First Normal Form (1NF): Each field contains an appropriate single value.
- Second Normal Form (2NF): Non-key attributes depend on the entire candidate key.
- Third Normal Form (3NF): Non-key attributes do not depend transitively on candidate keys.

A complete 3NF validation should be documented using the actual database schema.

### Primary Keys and Foreign Keys

Primary keys uniquely identify records within their respective tables.

Foreign keys establish relationships between related database entities and help maintain referential integrity.

Foreign-key deletion behavior should use appropriate actions depending on the relationship:

- ON DELETE CASCADE
- ON DELETE SET NULL
- ON DELETE RESTRICT

The final deletion rules must be verified against the implemented SQL constraints.

---

## 8. Database Relationships

The system uses relational associations to connect construction management records.

### Worker Skills

The `worker_skills` associative table connects workers and skills.

This relationship allows a worker to be associated with multiple skills, while a skill can be associated with multiple workers.

### Task Assignments

The `task_assignments` associative table connects workers and tasks.

This relationship allows workers to be assigned to construction tasks through a separate assignment record.

### Other Relationships

Additional relationships connect:

- Clients and projects.
- Projects and project members.
- Projects and project phases.
- Projects and tasks.
- Tasks and material usage.
- Materials and material usage.
- Suppliers and related material records, where configured.
- Equipment and equipment usage.
- Projects and expenses.
- Users and audit logs.

The exact relationship cardinalities and foreign-key constraints should be documented from the actual schema.

---

## 9. Database Automation

The project includes SQL scripts for defining database structures and database routines.

### SQL Scripts

| File | Description |
|---|---|
| `001_schema.sql` | Contains database schema definitions. |
| `002_routines_and_triggers.sql` | Contains database routine and trigger definitions. |

### Database Triggers

Triggers can automate database operations when specified events occur.

Potential applications include:

- Recording selected database changes in audit logs.
- Automatically maintaining standard record values.
- Supporting data consistency during database operations.

Core audit logging requirements should be implemented and demonstrated at the database level where required by the project guidelines.

### Stored Procedures and Functions

Stored procedures and functions can centralize complex database operations and business rules.

Potential applications include:

- Project-related calculations.
- Bulk data operations.
- Construction expense calculations.
- Resource monitoring operations.

The actual implemented procedures and functions should be documented after reviewing and testing `002_routines_and_triggers.sql`.

### Transactions and ACID Properties

Transactions should be used for operations that involve multiple related database changes.

The system's database engineering should consider the four ACID properties:

| Property | Description |
|---|---|
| Atomicity | All operations in a transaction succeed together or are rolled back. |
| Consistency | Transactions maintain database rules and constraints. |
| Isolation | Concurrent transactions are controlled to prevent improper interference. |
| Durability | Committed changes remain stored after successful completion. |

Transaction implementation and testing should be demonstrated using actual application operations.

---

## 10. Security

The application uses several security mechanisms.

### Authentication

JSON Web Tokens (JWT) are used to authenticate users and protect restricted application routes.

### Password Protection

Passwords are hashed using bcryptjs before being stored in the database.

### Authorization

Role-based middleware checks the user's assigned role before allowing access to protected operations.

### Environment Variables

Sensitive application configuration is stored in environment variables rather than hardcoded into source files.

### Database Security

Production database security should include:

- Parameterized database operations.
- Least-privilege database accounts.
- Restricted database access.
- Secure environment variable configuration.
- Encrypted database connections.
- Proper management of database credentials.

Sensitive credentials, including database passwords and JWT secrets, must never be committed to the GitHub repository.

---

## 11. Project Structure

The following is the general organization of the application:

```text
CONSTRUCTION-MANAGEMENT-SYSTEM/
│
├── src/
│   ├── app.js
│   │
│   ├── config/
│   │   └── database.js
│   │
│   ├── controllers/
│   │
│   ├── middleware/
│   │
│   ├── models/
│   │
│   ├── routes/
│   │
│   ├── services/
│   │
│   └── views/
│       ├── assignments/
│       ├── attendance/
│       ├── audit-logs/
│       ├── clients/
│       ├── dashboard/
│       ├── equipment/
│       ├── equipment-usage/
│       ├── expenses/
│       ├── login/
│       ├── material-usage/
│       ├── materials/
│       ├── phases/
│       ├── project-members/
│       ├── projects/
│       ├── reports/
│       ├── suppliers/
│       ├── tasks/
│       ├── user-profiles/
│       ├── users/
│       ├── worker-skills/
│       └── workers/
│
├── database/
│   ├── 001_schema.sql
│   └── 002_routines_and_triggers.sql
│
├── scripts/
│
├── public/
│
├── docs/
│
├── .env.example
├── .gitignore
├── package.json
├── package-lock.json
└── README.md
```

This is a logical overview of the project structure. Actual filenames and folders may vary depending on the current repository.

---

## 12. Installation and Setup

### Prerequisites

Before running the project locally, install:

- Node.js
- PostgreSQL
- Git
- Visual Studio Code

### Step 1: Clone the Repository

```bash
git clone https://github.com/idul23/CONSTRUCTION-MANAGEMENT-SYSTEM.git
```

### Step 2: Open the Project Directory

```bash
cd CONSTRUCTION-MANAGEMENT-SYSTEM
```

### Step 3: Install Dependencies

```bash
npm install
```

### Step 4: Configure Environment Variables

Create a `.env` file in the project root.

Use `.env.example` as a reference if it is available.

Configure the required database connection, authentication secret, and application settings.

Example configuration:

```env
PORT=3000
DATABASE_URL=your_postgresql_connection_string
JWT_SECRET=your_secure_secret
NODE_ENV=development
```

Replace the placeholder values with your own local development configuration.

Never commit the actual `.env` file to GitHub.

### Step 5: Create the Local Database

Open PostgreSQL and create the development database:

```sql
CREATE DATABASE construction_management;
```

### Step 6: Initialize the Database

Execute the database scripts in the correct order:

```text
database/001_schema.sql
database/002_routines_and_triggers.sql
```

Make sure the database schema is successfully created before starting the application.

### Step 7: Start the Application

```bash
node src/app.js
```

---

## 13. Running the Application

After successfully starting the Express.js server, open the following address in your browser:

http://localhost:3000

Ensure that:

- PostgreSQL is running.
- The database exists.
- The environment variables are configured correctly.
- Required database tables and relationships are initialized.
- All application dependencies are installed.

---

## 14. Production Deployment

The application is deployed using Render.

### Deployment Architecture

| Component | Platform |
|---|---|
| Source Code Repository | GitHub |
| Web Application Hosting | Render Web Service |
| Backend Runtime | Node.js |
| Backend Framework | Express.js |
| Database Hosting | Render PostgreSQL |
| Relational Database | PostgreSQL |
| ORM | Sequelize |

### Live Application URL

https://construction-management-system-5ybe.onrender.com

### Production Environment Variables

The Render Web Service uses environment variables to configure the production application.

| Variable | Purpose |
|---|---|
| `DATABASE_URL` | PostgreSQL database connection string |
| `JWT_SECRET` | Authentication secret |
| `NODE_ENV` | Application environment |

Additional environment variables may be required depending on the application configuration.

### Deployment Process

1. Push the application changes to the connected GitHub branch.
2. Configure the Render Web Service.
3. Configure the production environment variables.
4. Connect the application to the production PostgreSQL database.
5. Deploy the application.
6. Review deployment logs.
7. Test the public website.

### Production Database Migration

The production PostgreSQL database must contain the required schema, constraints, routines, triggers, and application records.

The local development database does not automatically transfer its tables and data to Render.

Database migration and data verification must be completed before considering production deployment fully operational.

### Hosting Considerations

The current Render PostgreSQL instance is on a free plan and displays an expiration date of November 30, 2026.

The hosting plan and database availability should be reviewed before final project submission.

---

## 15. Git and GitHub Workflow

The project uses Git and GitHub for source code management and collaboration.

Development follows a feature branch and Pull Request workflow.

Direct pushes to the `main` branch are not permitted under the project's course workflow requirements.

### Create a Feature Branch

```bash
git checkout -b feature/feature-name
```

### Stage Changes

```bash
git add .
```

### Commit Changes

```bash
git commit -m "feat: describe implemented feature"
```

### Push the Feature Branch

```bash
git push origin feature/feature-name
```

### Create a Pull Request

1. Open the GitHub repository.
2. Select the Pull Requests section.
3. Create a new Pull Request.
4. Set the target branch to `main`.
5. Describe the changes.
6. Review and merge according to the team's workflow.

### Commit Message Convention

| Prefix | Purpose |
|---|---|
| `feat:` | New functionality |
| `fix:` | Bug fixes |
| `db:` | Database changes |
| `perf:` | Performance improvements |
| `docs:` | Documentation updates |
| `security:` | Security improvements |
| `refactor:` | Code restructuring |

### Example Commit Messages

```text
feat: add project management functionality
fix: correct attendance record handling
db: update database schema constraints
perf: optimize project queries
docs: update system documentation
security: improve route authorization
```

---

## 16. Development Status

The following table distinguishes existing application components from database and production requirements that still need verification.

| Component | Status |
|---|---|
| Node.js and Express.js backend | Implemented |
| PostgreSQL integration | Implemented for development |
| Sequelize ORM | Implemented |
| EJS user interface | Implemented |
| Authentication | Implemented |
| Role-based access control | Implemented |
| Construction management modules | Existing implementation |
| SQL schema scripts | Existing |
| Render Web Service | Deployed |
| Render PostgreSQL instance | Created |
| Production database schema migration | Requires verification |
| Production data migration | Requires verification |
| 3NF normalization audit | Requires verification |
| Foreign-key deletion policies | Requires verification |
| Database triggers and routines | Requires verification |
| Stored procedure testing | Requires verification |
| Transaction testing | Requires verification |
| Query optimization | Requires testing |
| Production database privileges | Requires verification |
| Stress and concurrency testing | Pending verification |
| Database dictionary | Pending |
| Query benchmark report | Pending |

---

## 17. Future Improvements

The following improvements are planned to strengthen the application and its database implementation:

- Complete database normalization validation up to Third Normal Form.
- Review primary keys, foreign keys, and relational constraints.
- Implement and verify database-side audit triggers.
- Expand stored procedure and function implementations.
- Improve transaction handling for related operations.
- Optimize database queries and indexes.
- Review database execution plans using EXPLAIN ANALYZE.
- Strengthen role-based access restrictions.
- Configure least-privilege production database credentials.
- Complete cloud database migration and validation.
- Perform concurrent request and stress testing.
- Prepare a complete database dictionary.
- Prepare query performance benchmark documentation.
- Review GitHub branch protection and Pull Request procedures.
- Maintain reliable production backups and recovery procedures.

---

## 18. Contributors

**Project:** Construction Project and Labor Management System

**Course:** Advanced Database Systems

**Project Category:** ERP / Construction Project Management

### Team Responsibilities

| Role | Assigned Member |
|---|---|
| Lead Database Administrator and Cloud Infrastructure Engineer | To be assigned |
| Lead Database Programmer and Query Optimization Engineer | To be assigned |
| Frontend/UI Connector and Git Workflow Administrator | To be assigned |

---

## License

This project is developed for academic and educational purposes as part of the Advanced Database Systems course.
