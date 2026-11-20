# Smart Attendance System Using Biometrics

## Project Overview

The Smart Attendance System is a web-based attendance management system that uses biometric fingerprint authentication to record student attendance automatically.

The system integrates Arduino Uno, ESP32, a fingerprint sensor, PHP, and MySQL to provide a centralized platform for managing attendance records.

## Technologies Used

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Development Environment:** XAMPP
- **Microcontrollers:** Arduino Uno, ESP32
- **Communication:** UART and Wi-Fi

## Hardware Components

- Fingerprint Sensor
- Arduino Uno
- ESP32 Wi-Fi Module
- Laptop or Computer

## Key Features

- Biometric fingerprint-based attendance
- Student management
- Faculty and subject management
- Automatic attendance recording
- Attendance reports and dashboard
- ESP32 device connectivity
- Parent notification records
- Student attendance dashboard

## System Architecture

Fingerprint Sensor → Arduino Uno → ESP32 → Wi-Fi → PHP API → MySQL Database → Web Dashboard

## Project Structure

- `api/` – API endpoints for device communication
- `assets/` – CSS and JavaScript files
- `config/` – Database and email configuration
- `database/` – SQL database file
- `hardware/` – ESP32 example firmware
- `includes/` – Common PHP components
- `vendor/` – External PHP dependencies (not included in GitHub)

## Installation and Setup

1. Install XAMPP on your computer.
2. Start Apache and MySQL.
3. Copy the project folder into the XAMPP `htdocs` directory.
4. Create the `smart_attendance` database using the provided SQL file.
5. Configure your local database settings using `config/database.example.php`.
6. Configure email settings using `config/email.example.php` if email notifications are required.
7. Open the project in your browser:
   `http://localhost/smart_attendance/`
8. Complete the required setup and create or configure your administrator account.

## Parent Notifications

The system maintains parent notification records for attendance-related alerts. Actual email or SMS delivery requires an appropriately configured external messaging service.

## Security Notes

- Never upload actual email credentials or private configuration files.
- Change default administrator credentials immediately.
- Delete or disable the installation script after setup.
- Do not expose the local development server directly to the public internet.
- Additional security measures are required before production deployment.

## Future Enhancements

- Real-time parent notifications
- Advanced attendance analytics
- Improved device authentication
- Cloud-based deployment
- Enhanced security and access control

## Project Purpose

This project demonstrates the integration of embedded systems, biometric authentication, database management, and web development to automate student attendance management.