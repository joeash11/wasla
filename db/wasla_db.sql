-- ============================================
-- Wasla Database Schema
-- Run this file in phpMyAdmin or MySQL CLI
-- ============================================

CREATE DATABASE IF NOT EXISTS wasla_db;
USE wasla_db;

-- ============================================
-- USERS TABLE
-- Stores all users: clients, ushers, admins
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('client', 'usher', 'admin') NOT NULL DEFAULT 'client',
    company_name VARCHAR(100) DEFAULT NULL,
    city VARCHAR(50) DEFAULT NULL,
    skills VARCHAR(255) DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    rating DECIMAL(3,2) DEFAULT 0.00,
    is_verified TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- PROJECTS TABLE
-- Events/projects created by clients
-- ============================================
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    end_date DATE DEFAULT NULL,
    location VARCHAR(100) NOT NULL,
    city VARCHAR(50) NOT NULL,
    ushers_needed INT NOT NULL DEFAULT 1,
    budget DECIMAL(10,2) DEFAULT NULL,
    pay_per_usher DECIMAL(10,2) DEFAULT NULL,
    status ENUM('pending', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    category VARCHAR(50) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- PROJECT APPLICATIONS TABLE
-- Ushers applying to projects
-- ============================================
CREATE TABLE project_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    usher_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (usher_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (project_id, usher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- MESSAGES TABLE
-- Messages between users
-- ============================================
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- REVIEWS TABLE
-- Client reviews of ushers after events
-- ============================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reviewer_id INT NOT NULL,
    usher_id INT NOT NULL,
    project_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (usher_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (reviewer_id, usher_id, project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TRANSACTIONS TABLE
-- Payment records
-- ============================================
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payer_id INT NOT NULL,
    payee_id INT NOT NULL,
    project_id INT DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('payment', 'refund', 'payout') NOT NULL DEFAULT 'payment',
    status ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    description VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (payee_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- CONTACT MESSAGES TABLE
-- Contact form submissions
-- ============================================
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(150) DEFAULT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- REPORTS TABLE
-- Reports filed by clients about ushers or
-- by ushers about clients after events
-- ============================================
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter_id INT NOT NULL,
    reported_id INT NOT NULL,
    project_id INT NOT NULL,
    reporter_role ENUM('client', 'usher') NOT NULL,
    reason ENUM('unprofessional', 'no_show', 'harassment', 'late', 'payment_issue', 'safety', 'other') NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'reviewed', 'resolved', 'dismissed') NOT NULL DEFAULT 'pending',
    admin_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reported_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SAMPLE DATA
-- ============================================

-- Admin user (email: admin@wasla.com  password: admin123)
INSERT INTO users (first_name, last_name, email, password, role, is_verified, is_active)
VALUES ('Admin', 'Wasla', 'admin@wasla.com', '$2y$10$TkZ6yH4VhGNt2Y0/S6QKoeltufWFRXNnjeM2UVvFYPoG9WEkoze06', 'admin', 1, 1);

-- Sample client (password: password)
INSERT INTO users (first_name, last_name, email, password, phone, role, company_name, city, is_verified, is_active)
VALUES ('Abdullah', 'Elsayed', 'abdullah@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+966 555 123 456', 'client', 'Gulf Events Co.', 'Riyadh', 1, 1);

-- Sample usher (password: password)
INSERT INTO users (first_name, last_name, email, password, phone, role, city, skills, rating, is_verified, is_active)
VALUES ('Ahmed', 'Mohamed', 'ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+966 555 789 012', 'usher', 'Riyadh', 'Customer Service, Bilingual, VIP Handling', 4.80, 1, 1);

-- Second client
INSERT INTO users (first_name, last_name, email, password, phone, role, company_name, city, is_verified, is_active)
VALUES ('Sara', 'Ahmed', 'sara@mdlbeast.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+966 555 456 789', 'client', 'MDLBEAST', 'Riyadh', 1, 1);

-- Second usher
INSERT INTO users (first_name, last_name, email, password, phone, role, city, skills, rating, is_verified, is_active)
VALUES ('Fatimah', 'Al-Saud', 'fatimah@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+966 555 321 654', 'usher', 'Riyadh', 'VIP Handling, Arabic, Fashion', 4.50, 1, 1);

-- ============================================
-- SAMPLE PROJECTS
-- ============================================
INSERT INTO projects (client_id, title, description, event_date, end_date, location, city, ushers_needed, pay_per_usher, status, category) VALUES
(2, 'Gaming Festival 2024', 'Annual gaming event featuring tournaments and exhibitions', '2024-06-28', '2024-06-29', 'Riyadh Exhibition Center', 'Riyadh', 20, 450.00, 'active', 'Festival'),
(4, 'MDLBEAST Soundstorm', 'Mega music festival in Riyadh', '2024-07-02', '2024-07-04', 'Banban, Riyadh', 'Riyadh', 40, 600.00, 'active', 'Festival'),
(2, 'Fashion Week Riyadh', 'International fashion week showcase', '2024-06-10', '2024-06-15', 'The Ritz-Carlton', 'Riyadh', 12, 550.00, 'completed', 'Fashion'),
(2, 'Annual Charity Gala', 'Black-tie charity dinner and fundraiser', '2024-06-05', '2024-06-08', 'Al Faisaliyah Hotel', 'Riyadh', 8, 380.00, 'completed', 'Corporate'),
(4, 'Corporate Summit KSA', 'Business leadership conference', '2024-07-10', '2024-07-11', 'Hilton Jeddah', 'Jeddah', 8, 380.00, 'active', 'Corporate'),
(2, 'Food Festival', 'Street food and culinary arts festival', '2024-06-01', '2024-06-01', 'Jeddah Corniche', 'Jeddah', 10, 320.00, 'cancelled', 'Festival'),
(2, 'Tech Innovation Expo', 'Technology and startup exhibition with demos and networking', '2024-08-05', '2024-08-06', 'DIFC Dubai', 'Dubai', 20, 500.00, 'pending', 'Corporate'),
(4, 'Riyadh Season Launch Party', 'Grand opening event for Riyadh Season entertainment', '2024-09-01', '2024-09-03', 'Boulevard Riyadh', 'Riyadh', 50, 550.00, 'pending', 'Festival');

-- ============================================
-- USHER APPLICATIONS (Ahmed = user 3)
-- ============================================
INSERT INTO project_applications (project_id, usher_id, status, applied_at, responded_at) VALUES
(1, 3, 'accepted', '2024-06-15 10:00:00', '2024-06-16 09:00:00'),
(2, 3, 'accepted', '2024-06-18 14:30:00', '2024-06-19 11:00:00'),
(3, 3, 'completed', '2024-05-20 09:00:00', '2024-05-21 10:00:00'),
(4, 3, 'completed', '2024-05-25 12:00:00', '2024-05-26 08:00:00'),
(5, 3, 'accepted', '2024-06-20 16:00:00', '2024-06-21 10:00:00'),
(6, 3, 'rejected', '2024-05-15 11:00:00', '2024-05-28 09:00:00'),
(1, 5, 'accepted', '2024-06-16 08:00:00', '2024-06-17 09:00:00');

-- ============================================
-- REVIEWS FOR AHMED (usher_id = 3)
-- ============================================
INSERT INTO reviews (reviewer_id, usher_id, project_id, rating, comment, created_at) VALUES
(2, 3, 3, 5, 'Ahmed was outstanding — professional, punctual, and great with guests. Will definitely hire again!', '2024-06-15 18:00:00'),
(4, 3, 4, 5, 'Very reliable and handled VIP guests with perfect etiquette.', '2024-06-08 22:00:00'),
(2, 3, 4, 4, 'Great work at the corporate summit. Would recommend.', '2024-05-28 17:00:00');

-- ============================================
-- TRANSACTIONS (Payouts to Ahmed)
-- ============================================
INSERT INTO transactions (payer_id, payee_id, project_id, amount, type, status, description, created_at) VALUES
(2, 3, 3, 550.00, 'payout', 'completed', 'Payment for Fashion Week Riyadh', '2024-06-16 12:00:00'),
(2, 3, 4, 380.00, 'payout', 'completed', 'Payment for Annual Charity Gala', '2024-06-09 12:00:00'),
(2, 3, 3, 200.00, 'payout', 'completed', 'Bonus for Fashion Week', '2024-06-17 10:00:00'),
(4, 3, 4, 150.00, 'payout', 'completed', 'Tips from Charity Gala', '2024-06-10 09:00:00'),
(2, 3, 1, 450.00, 'payout', 'pending', 'Advance for Gaming Festival 2024', '2024-06-25 14:00:00'),
(4, 3, 2, 600.00, 'payout', 'pending', 'Advance for MDLBEAST Soundstorm', '2024-06-28 10:00:00');

-- ============================================
-- SAMPLE REPORTS
-- ============================================
INSERT INTO reports (reporter_id, reported_id, project_id, reporter_role, reason, description, status, created_at) VALUES
(2, 3, 3, 'client', 'late', 'The usher arrived 45 minutes late to the event and missed the VIP check-in period.', 'pending', '2024-06-16 10:00:00'),
(3, 2, 6, 'usher', 'payment_issue', 'I completed the event but payment has not been released after 2 weeks.', 'pending', '2024-06-20 14:00:00'),
(4, 5, 2, 'client', 'unprofessional', 'Was on phone during guest handling and did not follow dress code requirements.', 'reviewed', '2024-06-10 09:00:00');
