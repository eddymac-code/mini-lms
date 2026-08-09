# Mini LMS

A lightweight Loan Management System built with **Laravel 13, PHP 8.5, Vue, and MySQL 8**.

The application demonstrates the core lifecycle of a digital lending system, from loan product configuration and loan origination through approval, M-Pesa disbursement, repayment scheduling, M-Pesa repayments, and transaction reconciliation.

Customers are stored in the system but **do not authenticate**. System users authenticate and manage lending operations.

## Key Features

- System user management
- Customer management
- Loan product management
- Loan origination and approval workflow
- Configurable loan terms and interest methods
- Flat-rate interest
- Declining-balance equal-installment amortization
- Weekly and monthly repayment terms
- Configurable repayment order
- Repayment schedules generated on disbursement
- One-time and recurring loan fees
- M-Pesa B2C loan disbursement
- M-Pesa Express/STK Push repayments
- Asynchronous M-Pesa callbacks
- Payment allocation against installments
- Loan transaction history
- Daraja callback persistence for audit and reconciliation
- Idempotent callback/payment processing
- Queue-based B2C disbursement processing

---

## Requirements

Make sure the following are installed:

- **PHP 8.5+**
- **Composer**
- **MySQL 8.0+**
- **Node.js / npm**
- **Git**

The application was developed and tested with **Laravel 13** and **PHP 8.5**.

---

## Local Setup

### 1. Clone the repository
 <!-- Bash -->
git clone <repository-url>
cd mini-lms

### 2. Install PHP dependencies
composer install

### 3. Install frontend dependencies
npm install

### 4. Configure the environment
cp .env.example .env

### 5. Generate the Laravel application key
php artisan key:generate

### 6. Configure MySQL
- Create an empty MySQL 8 database, for example: mini_lms
- Update the database configuration in .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_lms
DB_USERNAME=root
DB_PASSWORD=your_password

### 6. Run migrations and seed the database
php artisan migrate:fresh --seed

## Running the Application
### Recommended development setup
- The development script can start the required development processes together using:
composer run dev

### Minimal/manual setup
#### Terminal 1 — Laravel application
php artisan serve

#### Terminal 2 — Vite frontend
npm run dev

#### Terminal 3 — Queue worker
php artisan queue:work

##### You can also use:
php artisan queue:listen

## M-Pesa / Daraja Configuration
The application integrates with Safaricom Daraja for:

- B2C loan disbursement
- M-Pesa Express/STK Push loan repayments
- B2C result callbacks
- B2C timeout callbacks
- STK callbacks


