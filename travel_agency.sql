-- ===============================================
-- TRAVEL AGENCY DATABASE SCHEMA
-- For use with WAMP MySQL Server
-- ===============================================

-- Create database
CREATE DATABASE IF NOT EXISTS travel_agency;
USE travel_agency;

-- ===============================================
-- TABLE: packages
-- Stores travel package information
-- ===============================================
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    icon VARCHAR(10),
    duration VARCHAR(50),
    destination VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================================
-- TABLE: bookings
-- Stores customer bookings
-- ===============================================
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    package_id INT NOT NULL,
    date DATE NOT NULL,
    people INT NOT NULL,
    requests TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    total_price DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES packages(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================================
-- TABLE: users
-- Stores admin and user accounts
-- VULNERABILITY: Plain text passwords stored
-- ===============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================================
-- TABLE: sessions (for demonstration)
-- Not actually used - showing missing auth vulnerability
-- ===============================================
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================================
-- INSERT SAMPLE DATA
-- ===============================================

-- Insert travel packages
INSERT INTO packages (name, description, price, icon, duration, destination) VALUES
('Tropical Paradise', 'Relax on pristine beaches with crystal clear waters. Includes beachfront resort, water sports, and spa treatments.', 1299.00, '🏝️', '7 days', 'Maldives'),
('Mountain Adventure', 'Explore breathtaking peaks and scenic trails. Perfect for hiking enthusiasts and nature lovers.', 899.00, '⛰️', '5 days', 'Swiss Alps'),
('City Explorer', 'Discover vibrant cultures and historic landmarks. Guided tours of major attractions included.', 799.00, '🏙️', '4 days', 'Paris, France'),
('Safari Experience', 'Witness wildlife in their natural habitat. Game drives and luxury lodge accommodation.', 1599.00, '🦁', '6 days', 'Kenya'),
('Cruise Getaway', 'Sail the seas with luxury and comfort. All-inclusive dining and entertainment.', 1999.00, '🚢', '10 days', 'Mediterranean'),
('Cultural Heritage', 'Immerse in ancient traditions and history. Visit UNESCO World Heritage sites.', 999.00, '🏛️', '5 days', 'Rome, Italy');

-- Insert admin users
-- VULNERABILITY: Plain text passwords (should be hashed!)
INSERT INTO users (username, password, role, email) VALUES
('admin', 'admin123', 'admin', 'admin@travelagency.com'),
('user', 'password', 'user', 'user@example.com'),
('manager', 'manager2024', 'manager', 'manager@travelagency.com');

-- Insert sample bookings for testing
INSERT INTO bookings (name, email, package_id, date, people, requests, status, total_price) VALUES
('John Doe', 'john.doe@email.com', 1, '2024-12-15', 2, 'Honeymoon suite please', 'confirmed', 2598.00),
('Jane Smith', 'jane.smith@email.com', 3, '2024-12-20', 1, 'Vegetarian meals', 'pending', 799.00),
('Bob Johnson', 'bob.johnson@email.com', 4, '2025-01-10', 4, 'Early check-in if possible', 'confirmed', 6396.00),
('Alice Brown', 'alice.brown@email.com', 2, '2024-12-25', 2, NULL, 'pending', 1798.00),
('Charlie Wilson', 'charlie.w@email.com', 5, '2025-02-14', 2, 'Anniversary celebration', 'confirmed', 3998.00);

-- ===============================================
-- CREATE INDEXES for better performance
-- ===============================================
CREATE INDEX idx_bookings_email ON bookings(email);
CREATE INDEX idx_bookings_date ON bookings(date);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_packages_price ON packages(price);

-- ===============================================
-- DISPLAY TABLE INFORMATION
-- ===============================================
SELECT 'Database setup complete!' AS Status;
SELECT COUNT(*) AS 'Total Packages' FROM packages;
SELECT COUNT(*) AS 'Total Users' FROM users;
SELECT COUNT(*) AS 'Total Bookings' FROM bookings;

-- ===============================================
-- SECURITY NOTES (For Educational Purposes)
-- ===============================================
-- This database intentionally contains vulnerabilities:
-- 1. Plain text password storage (should use bcrypt/argon2)
-- 2. No prepared statement enforcement
-- 3. Weak password policy
-- 4. Missing rate limiting tables
-- 5. No audit/logging tables
-- 
-- DO NOT USE IN PRODUCTION!
-- ===============================================