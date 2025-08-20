-- Mega School Plaza Database Schema and Seed Data
-- Run this file to create the complete database structure

SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS megaplaza;
CREATE DATABASE megaplaza CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE megaplaza;

-- USERS & AUTH
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role ENUM('super_admin','manager','tenant') NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  phone VARCHAR(30),
  password_hash VARCHAR(255) NOT NULL,
  email_verified TINYINT(1) DEFAULT 0,
  status ENUM('active','disabled') DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE user_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  session_id VARCHAR(128) NOT NULL,
  ip VARCHAR(45),
  user_agent VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- SHOPS
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) UNIQUE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE amenities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) UNIQUE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE floors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(40) UNIQUE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE shops (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) UNIQUE NOT NULL,
  name VARCHAR(120) NOT NULL,
  category_id INT,
  floor_id INT,
  size_sqm DECIMAL(8,2) NOT NULL,
  rent_monthly DECIMAL(12,2) NOT NULL,
  deposit_amount DECIMAL(12,2) DEFAULT 0,
  status ENUM('available','reserved','occupied') DEFAULT 'available',
  description TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (floor_id) REFERENCES floors(id)
) ENGINE=InnoDB;

CREATE TABLE shop_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  shop_id INT NOT NULL,
  path VARCHAR(255) NOT NULL,
  is_cover TINYINT(1) DEFAULT 0,
  FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE shop_amenities (
  shop_id INT NOT NULL,
  amenity_id INT NOT NULL,
  PRIMARY KEY (shop_id, amenity_id),
  FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
  FOREIGN KEY (amenity_id) REFERENCES amenities(id)
) ENGINE=InnoDB;

-- APPLICATIONS & LEASES
CREATE TABLE applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  shop_id INT NOT NULL,
  tenant_id INT NOT NULL,
  business_name VARCHAR(160) NOT NULL,
  message TEXT,
  status ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (shop_id) REFERENCES shops(id),
  FOREIGN KEY (tenant_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE leases (
  id INT AUTO_INCREMENT PRIMARY KEY,
  shop_id INT NOT NULL,
  tenant_id INT NOT NULL,
  application_id INT,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  billing_cycle ENUM('monthly','quarterly','annually') DEFAULT 'monthly',
  rent_amount DECIMAL(12,2) NOT NULL,
  deposit_amount DECIMAL(12,2) DEFAULT 0,
  service_charge DECIMAL(12,2) DEFAULT 0,
  status ENUM('draft','active','terminated','expired') DEFAULT 'draft',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (shop_id) REFERENCES shops(id),
  FOREIGN KEY (tenant_id) REFERENCES users(id),
  FOREIGN KEY (application_id) REFERENCES applications(id)
) ENGINE=InnoDB;

-- BILLING
CREATE TABLE invoices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lease_id INT NOT NULL,
  invoice_no VARCHAR(40) UNIQUE NOT NULL,
  due_date DATE NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  status ENUM('unpaid','paid','overdue') DEFAULT 'unpaid',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (lease_id) REFERENCES leases(id)
) ENGINE=InnoDB;

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT NOT NULL,
  payment_date DATE NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  method ENUM('cash','transfer','online') DEFAULT 'cash',
  reference VARCHAR(100),
  attachment VARCHAR(255),
  created_by INT,
  FOREIGN KEY (invoice_id) REFERENCES invoices(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- TICKETS
CREATE TABLE tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lease_id INT NOT NULL,
  title VARCHAR(160) NOT NULL,
  description TEXT,
  status ENUM('open','in_progress','resolved','closed') DEFAULT 'open',
  created_by INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (lease_id) REFERENCES leases(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE ticket_comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT NOT NULL,
  user_id INT NOT NULL,
  comment TEXT NOT NULL,
  attachment VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- NOTIFICATIONS & AUDIT
CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(160) NOT NULL,
  body TEXT,
  is_read TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  action VARCHAR(160) NOT NULL,
  details TEXT,
  ip VARCHAR(45),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- SEED DATA

-- Insert Categories
INSERT INTO categories (name) VALUES 
('Retail'), ('Barber Shop'), ('Services'), ('Restaurant'), ('Electronics');

-- Insert Amenities
INSERT INTO amenities (name) VALUES 
('Power Backup'), ('Water Supply'), ('Air Conditioning'), ('WiFi'), ('Parking'), ('Security');

-- Insert Floors
INSERT INTO floors (name) VALUES 
('Ground Floor'), ('First Floor'), ('Second Floor');

-- Insert Users
INSERT INTO users (role, full_name, email, phone, password_hash, email_verified, status) VALUES
('super_admin', 'Plaza Owner', 'admin@megaplaza.com', '+234-800-0000-001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active'),
('manager', 'John Manager', 'manager1@megaplaza.com', '+234-800-0000-002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active'),
('manager', 'Jane Manager', 'manager2@megaplaza.com', '+234-800-0000-003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active'),
('tenant', 'Alice Tenant', 'tenant1@example.com', '+234-800-0000-004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active'),
('tenant', 'Bob Tenant', 'tenant2@example.com', '+234-800-0000-005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active'),
('tenant', 'Carol Tenant', 'tenant3@example.com', '+234-800-0000-006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active'),
('tenant', 'David Tenant', 'tenant4@example.com', '+234-800-0000-007', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active'),
('tenant', 'Eva Tenant', 'tenant5@example.com', '+234-800-0000-008', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'active');

-- Insert Shops
INSERT INTO shops (code, name, category_id, floor_id, size_sqm, rent_monthly, deposit_amount, status, description) VALUES
('GF-001', 'Prime Corner Shop', 1, 1, 45.50, 150000.00, 300000.00, 'available', 'Perfect corner location with high foot traffic'),
('GF-002', 'Modern Barber Shop', 2, 1, 25.00, 80000.00, 160000.00, 'occupied', 'Fully equipped barber shop with modern fixtures'),
('GF-003', 'Electronics Store', 5, 1, 35.75, 120000.00, 240000.00, 'available', 'Ideal for electronics and gadgets retail'),
('GF-004', 'Fashion Boutique', 1, 1, 40.00, 130000.00, 260000.00, 'available', 'Spacious boutique with excellent lighting'),
('GF-005', 'Quick Service Restaurant', 4, 1, 55.25, 200000.00, 400000.00, 'reserved', 'Restaurant space with kitchen facilities'),
('FF-001', 'Professional Services Office', 3, 2, 30.00, 100000.00, 200000.00, 'available', 'Professional office space for services'),
('FF-002', 'Beauty Salon', 2, 2, 28.50, 90000.00, 180000.00, 'occupied', 'Modern beauty salon with all amenities'),
('FF-003', 'Retail Clothing Store', 1, 2, 42.00, 140000.00, 280000.00, 'available', 'Large retail space perfect for clothing'),
('FF-004', 'Tech Repair Shop', 3, 2, 22.75, 75000.00, 150000.00, 'available', 'Compact space ideal for tech repairs'),
('FF-005', 'Pharmacy', 3, 2, 38.00, 125000.00, 250000.00, 'available', 'Licensed pharmacy space with storage'),
('SF-001', 'Executive Office Suite', 3, 3, 50.00, 180000.00, 360000.00, 'available', 'Premium office suite with city view'),
('SF-002', 'Fitness Studio', 3, 3, 65.00, 220000.00, 440000.00, 'available', 'Large open space perfect for fitness'),
('SF-003', 'Art Gallery', 1, 3, 48.25, 160000.00, 320000.00, 'available', 'Gallery space with excellent lighting'),
('SF-004', 'Consulting Office', 3, 3, 32.50, 110000.00, 220000.00, 'occupied', 'Professional consulting office'),
('SF-005', 'Specialty Restaurant', 4, 3, 60.00, 210000.00, 420000.00, 'available', 'Premium restaurant space with terrace'),
('GF-006', 'Mobile Phone Shop', 5, 1, 20.00, 70000.00, 140000.00, 'available', 'Compact shop for mobile phones and accessories'),
('GF-007', 'Jewelry Store', 1, 1, 18.50, 85000.00, 170000.00, 'available', 'Secure jewelry store with display cases'),
('FF-006', 'Tailoring Shop', 3, 2, 24.00, 65000.00, 130000.00, 'available', 'Traditional tailoring shop with workspace'),
('FF-007', 'Bookstore & Cafe', 1, 2, 45.00, 135000.00, 270000.00, 'available', 'Combined bookstore and cafe space'),
('SF-006', 'Photography Studio', 3, 3, 40.00, 145000.00, 290000.00, 'available', 'Professional photography studio with equipment');

-- Insert Shop Amenities (sample associations)
INSERT INTO shop_amenities (shop_id, amenity_id) VALUES
(1, 1), (1, 2), (1, 6), -- Prime Corner Shop
(2, 1), (2, 2), (2, 3), -- Modern Barber Shop
(3, 1), (3, 3), (3, 4), (3, 6), -- Electronics Store
(4, 1), (4, 2), (4, 3), (4, 4), -- Fashion Boutique
(5, 1), (5, 2), (5, 3), (5, 6), -- Quick Service Restaurant
(6, 1), (6, 3), (6, 4), (6, 6), -- Professional Services Office
(7, 1), (7, 2), (7, 3), (7, 4), -- Beauty Salon
(8, 1), (8, 3), (8, 4), (8, 6), -- Retail Clothing Store
(9, 1), (9, 4), (9, 6), -- Tech Repair Shop
(10, 1), (10, 2), (10, 3), (10, 6); -- Pharmacy

-- Insert Sample Applications
INSERT INTO applications (shop_id, tenant_id, business_name, message, status) VALUES
(1, 4, 'Alice Fashion Store', 'I would like to rent this corner shop for my fashion business. I have 5 years experience in retail.', 'pending'),
(3, 5, 'Bob Electronics Hub', 'Perfect location for my electronics store. I can provide references from previous locations.', 'approved'),
(6, 6, 'Carol Consulting Services', 'Professional office space needed for my consulting business. Ready to start immediately.', 'pending'),
(11, 7, 'David Executive Solutions', 'Looking for premium office space for my growing business.', 'rejected'),
(15, 8, 'Eva Gourmet Restaurant', 'Experienced restaurateur seeking premium location for fine dining establishment.', 'approved');

-- Insert Sample Leases
INSERT INTO leases (shop_id, tenant_id, application_id, start_date, end_date, billing_cycle, rent_amount, deposit_amount, service_charge, status) VALUES
(2, 4, NULL, '2024-01-01', '2025-12-31', 'monthly', 80000.00, 160000.00, 5000.00, 'active'),
(7, 5, NULL, '2024-02-01', '2026-01-31', 'monthly', 90000.00, 180000.00, 6000.00, 'active'),
(14, 6, NULL, '2024-03-01', '2025-02-28', 'monthly', 110000.00, 220000.00, 7000.00, 'active'),
(3, 5, 2, '2024-12-01', '2026-11-30', 'monthly', 120000.00, 240000.00, 8000.00, 'draft'),
(15, 8, 5, '2025-01-01', '2027-12-31', 'monthly', 210000.00, 420000.00, 15000.00, 'draft');

-- Insert Sample Invoices
INSERT INTO invoices (lease_id, invoice_no, due_date, amount, status) VALUES
(1, 'INV-2024-001', '2024-01-05', 85000.00, 'paid'),
(1, 'INV-2024-002', '2024-02-05', 85000.00, 'paid'),
(1, 'INV-2024-003', '2024-03-05', 85000.00, 'overdue'),
(2, 'INV-2024-004', '2024-02-05', 96000.00, 'paid'),
(2, 'INV-2024-005', '2024-03-05', 96000.00, 'unpaid'),
(3, 'INV-2024-006', '2024-03-05', 117000.00, 'paid'),
(3, 'INV-2024-007', '2024-04-05', 117000.00, 'unpaid');

-- Insert Sample Payments
INSERT INTO payments (invoice_id, payment_date, amount, method, reference, created_by) VALUES
(1, '2024-01-03', 85000.00, 'transfer', 'TXN-001-2024', 2),
(2, '2024-02-04', 85000.00, 'cash', 'CASH-002-2024', 2),
(4, '2024-02-03', 96000.00, 'transfer', 'TXN-004-2024', 2),
(6, '2024-03-04', 117000.00, 'online', 'PAY-006-2024', 3);

-- Insert Sample Tickets
INSERT INTO tickets (lease_id, title, description, status, created_by) VALUES
(1, 'Air Conditioning Not Working', 'The AC unit in my shop has stopped working. Please send a technician.', 'open', 4),
(2, 'Water Leak in Ceiling', 'There is a water leak coming from the ceiling near the entrance.', 'in_progress', 5),
(3, 'Electrical Issue', 'Some power outlets are not working properly.', 'resolved', 6);

-- Insert Sample Ticket Comments
INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES
(1, 4, 'The AC stopped working yesterday evening. It was working fine before.'),
(1, 2, 'We have scheduled a technician visit for tomorrow morning.'),
(2, 5, 'The leak is getting worse and affecting my business operations.'),
(2, 2, 'Maintenance team is working on it. Should be fixed by end of day.'),
(3, 6, 'All outlets are now working properly. Thank you for the quick response.'),
(3, 3, 'Glad we could resolve this quickly. Ticket closed.');

-- Insert Sample Notifications
INSERT INTO notifications (user_id, title, body, is_read) VALUES
(4, 'Application Approved', 'Your application for shop GF-003 has been approved. Please check your lease details.', 0),
(5, 'Invoice Due', 'Your invoice INV-2024-005 is due on March 5th. Please make payment to avoid late fees.', 0),
(6, 'Ticket Updated', 'Your maintenance ticket #3 has been resolved. Please check and confirm.', 1),
(2, 'New Application', 'New application received for shop SF-001 from David Executive Solutions.', 0),
(1, 'Monthly Report', 'Monthly occupancy and revenue report is ready for review.', 0);

-- Insert Sample Audit Logs
INSERT INTO audit_logs (user_id, action, details, ip) VALUES
(1, 'User Login', 'Super admin logged in', '192.168.1.100'),
(2, 'Application Approved', 'Approved application ID 2 for shop GF-003', '192.168.1.101'),
(4, 'Payment Recorded', 'Recorded payment of ₦85,000 for invoice INV-2024-001', '192.168.1.102'),
(3, 'Ticket Created', 'Created maintenance ticket for lease ID 2', '192.168.1.103'),
(2, 'Lease Activated', 'Activated lease ID 3 for shop SF-004', '192.168.1.101');

SET FOREIGN_KEY_CHECKS = 1;
