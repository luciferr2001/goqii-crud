# GOQii-CRUD Setup Guide

This project consists of a frontend built with Next.js and a backend built with CodeIgniter 4. Follow the steps below to set up the project.

## Requirements

- **XAMPP**
- **Node.js** (latest version or v20.11)
- **pnpm**
- **Composer**

---

## Backend Setup

1. **Install Dependencies**

   - Navigate to the backend folder and run the following command to install all dependencies:
     ```bash
     composer install
     ```

2. **Start XAMPP**

   - Open the XAMPP Control Panel.
   - Start the **Apache** and **MySQL** services.

3. **Set Up the Database**

   - Open **phpMyAdmin**.
   - Create a new database with the name `goqii_user_management`.

4. **Run Migrations**

   - Run the following command to execute the database migrations:
     ```bash
     php spark migrate
     ```

5. **Authentication Tables**

   - Note: Additional tables for authentication purposes are included. These demonstrate a combination of stateless and stateful authentication mechanisms.

6. **Start the Backend Server**

   - Use the following command to start the server:
     ```bash
     php spark serve
     ```
   - The server will run on port `8080`.
   - The URL is hardcoded to use this port.

7. **Postman Collection**
   - A Postman collection is provided for testing the API endpoints.

---

## Frontend Setup

1. **Install Dependencies**

   - Navigate to the frontend folder and run the following command:
     ```bash
     pnpm install
     ```

2. **Start the Frontend**
   - Use the following command to run the application locally:
     ```bash
     pnpm run dev
     ```

---

Note i have added a fake auth token only for demo purposes
