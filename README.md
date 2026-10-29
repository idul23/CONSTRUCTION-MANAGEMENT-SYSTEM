
# Construction Project and Labor Management System

A web-based system for managing construction projects, workers, tasks, materials, equipment, attendance, expenses, and project reports.

## Features

- Project Management
- Worker and Skill Management
- Project Phases and Tasks
- Task Assignments
- Worker Attendance Tracking
- Material Inventory and Usage
- Supplier Management
- Equipment and Equipment Usage
- Expense Management
- Reports and Audit Logs
- User and Profile Management
- Role-Based Access Control

## Technologies

- Node.js
- Express.js
- PostgreSQL
- Sequelize ORM
- EJS
- HTML and CSS
- JavaScript
- Git and GitHub

## System Roles

### Administrator
Manages users, workers, projects, materials, and system records.

### Project Manager
Manages projects, assigns workers to tasks, monitors progress, and oversees expenses.

### Site Staff
Records attendance, work hours, material usage, and task progress.

## Project Structure

- `src/config/` — Database configuration
- `src/controllers/` — Application logic
- `src/middleware/` — Authentication and security middleware
- `src/models/` — Sequelize models and relationships
- `src/routes/` — Application routes
- `src/views/` — EJS templates
- `public/` — Static assets

## Prerequisites

- Node.js and npm
- PostgreSQL database

## Environment Configuration

Create a `.env` file in the project root with your database connection and application settings.

Required variables:

- `DATABASE_URL`
- `JWT_SECRET`
- `PORT` (optional; defaults to 3000)
- `NODE_ENV` (optional)

Never commit `.env` or expose database credentials.

## How to Run

1. Clone the repository.
2. Open the project folder in VS Code.
3. Install dependencies:

   `npm install`

4. Configure your `.env` file with valid PostgreSQL connection details.
5. Start the application:

   `node src/app.js`

6. Open `http://localhost:3000` in your browser.

The database must already exist, and the required tables and relationships must be configured before the application can work correctly.

## Project Status

Currently under development.
