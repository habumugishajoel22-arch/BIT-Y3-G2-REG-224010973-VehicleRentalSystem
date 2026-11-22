-- Vehicle Rental System: schema.sql
DROP DATABASE IF EXISTS vehicle_rental;
CREATE DATABASE vehicle_rental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vehicle_rental;

-- Staff / admin accounts (registration + login with password_hash)
CREATE TABLE users (
    user_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('admin','staff') NOT NULL DEFAULT 'staff',
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE vehicles (
    vehicle_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plate_no   VARCHAR(15)  NOT NULL UNIQUE,
    model      VARCHAR(80)  NOT NULL,
    type       ENUM('car','suv','van','bus','motorbike') NOT NULL,
    daily_rate DECIMAL(10,2) NOT NULL CHECK (daily_rate > 0),
    status     ENUM('available','rented','maintenance') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB;

CREATE TABLE customers (
    customer_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100) NOT NULL,
    license_no  VARCHAR(30)  NOT NULL UNIQUE,
    phone       VARCHAR(20)  NOT NULL,
    email       VARCHAR(120) NULL
) ENGINE=InnoDB;

CREATE TABLE rentals (
    rental_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id  INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    start_date  DATE NOT NULL,
    end_date    DATE NOT NULL,
    total_cost  DECIMAL(12,2) NOT NULL DEFAULT 0,
    status      ENUM('active','returned','cancelled') NOT NULL DEFAULT 'active',
    CONSTRAINT fk_rental_vehicle  FOREIGN KEY (vehicle_id)
        REFERENCES vehicles(vehicle_id)   ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_rental_customer FOREIGN KEY (customer_id)
        REFERENCES customers(customer_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_dates CHECK (end_date >= start_date)
) ENGINE=InnoDB;

CREATE INDEX idx_rentals_vehicle  ON rentals(vehicle_id);
CREATE INDEX idx_rentals_customer ON rentals(customer_id);
