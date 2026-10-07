

CREATE DATABASE IF NOT EXISTS crm_db;
USE crm_db;

-- ---------------------------------------------------------
-- Table: users  (login accounts for staff/admin)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,   -- stored as a hashed password
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Table: customers
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    company VARCHAR(100),
    address VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Table: leads  (business opportunities linked to a customer)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    status ENUM('New', 'In Progress', 'Won', 'Lost') DEFAULT 'New',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);



-- DDL: Creating the structural tables
CREATE TABLE IF NOT EXISTS sales_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    deal_name VARCHAR(100) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    stage ENUM('Prospecting', 'Proposal', 'Won', 'Lost') DEFAULT 'Prospecting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Open', 'In Progress', 'Closed') DEFAULT 'Open',
    priority ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS communication_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    interaction_type ENUM('Call', 'Email', 'Meeting') NOT NULL,
    notes TEXT NOT NULL,
    interaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- DML: Seeding relational data (Assumes customer ID 1 exists)
INSERT INTO sales_tracking (customer_id, deal_name, amount, stage) VALUES
(1, 'Enterprise Software License', 5000.00, 'Prospecting'),
(1, 'Cloud Migration Service', 12500.00, 'Proposal'),
(1, 'Annual Support Package', 2500.00, 'Won');

INSERT INTO support_tickets (customer_id, subject, message, status, priority) VALUES
(1, 'Cannot reset password', 'User is locked out and needs a reset link.', 'Open', 'High'),
(1, 'Invoice clarification', 'Question about the tax breakdown on Invoice #402.', 'In Progress', 'Low');

INSERT INTO communication_history (customer_id, interaction_type, notes) VALUES
(1, 'Call', 'Discussed initial project requirements and baseline budget limits.'),
(1, 'Email', 'Sent over the formal platform proposal deck.'),
(1, 'Meeting', 'Met in person to finalize details. Closed the annual package deal.');

USE crm_db;

-- Explicitly update the records to match the ENUM casing exactly
UPDATE sales_tracking SET stage = 'Prospecting' WHERE deal_name = 'Enterprise Software License';
UPDATE sales_tracking SET stage = 'Proposal' WHERE deal_name = 'Cloud Migration Service';
UPDATE sales_tracking SET stage = 'Won' WHERE deal_name = 'Annual Support Package';
