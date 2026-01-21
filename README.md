# Tanaman Dabaw Operations Management System

A web-based management system developed using **Laravel** to manage the clients, projects and employees for Tanaman Dabaw a Landscaping firm.

## Features

- One-time setup process for Admin/Owner account creation.
- User Authentication
- Automatically create user account when new employee is added.
- Upload images for project documentation.
- Responsive UI

## Subsystems

- Client Management System
- Project Management System
- Employee Management System
- Quotes and Invoice System (TODO: Backend Implementation)
- Basic Inventory System (TODO: Backend Implementation)


## User Roles

- **Admin**
  - Manage Employees, Clients, Projects, Quotes & Invoice and Inventory

- **Employee/Non-admin user**
  - Upload Images for Assigned project Documentation.

## Technologies Used

- Backend: Laravel
- Frontend: Blade, CSS
- Database: MySQL
- Image/File Storage: Amazon S3/ Backblaze S3 API

## Installation Guide

### Prerequisites

- PHP
- composer
- git
- nodejs
- npm

### 1. Clone the repository with the command

```bash
git clone https://github.com/amclaude/TanamanDabawOperationsManagementSystem
```

### 2. Install Depedencies
```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
```
**This project uses S3 API to store images**. Get any file storage that supports S3 API such as **Backblaze**.

```bash
B2_KEY_ID=your_key_id
B2_KEY_SECRET=your_key_secret
B2_BUCKET=your_bucket_name
B2_REGION=us-east-005
B2_ENDPOINT=https://s3.us-east-005.backblazeb2.com

```

### 4. Start Development Server

```bash
# make migrations
php artisan migrate
# Start the development server
php artisan serve
```



