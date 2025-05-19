# Personal Finance Management System

A simple yet effective personal finance tracking system built with PHP, MySQL, and Bootstrap 5.

## Features

- User Authentication (registration, login, profile management)
- Transaction Management (add, edit, delete income and expenses)
- Category Management (preset categories with ability to add custom ones)
- Dashboard with financial summaries and charts
- Export transactions to CSV
- Security features (input validation, password hashing, session handling)

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)

## Installation

1. Clone or download this repository to your web server directory
2. Create a new MySQL database
3. Import the `database/setup.sql` file to set up the database structure
4. Update the database configuration in `config/config.php` with your credentials
5. Navigate to the application URL in your browser
6. Register a new user account and start tracking your finances!

## Tech Stack

- Frontend: HTML, CSS, JavaScript, Bootstrap 5
- Backend: PHP
- Database: MySQL
- Charts: Chart.js

## Database Structure

- **users**: User account information
- **categories**: Transaction categories (income/expense)
- **transactions**: Financial transactions

## License

This project is licensed under the MIT License - see the LICENSE file for details.
