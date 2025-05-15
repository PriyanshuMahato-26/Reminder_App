CREATE DATABASE IF NOT EXISTS remind_me_later;

USE remind_me_later;

CREATE TABLE IF NOT EXISTS reminders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reminder_date DATE NOT NULL,
    reminder_time TIME NOT NULL,
    message TEXT NOT NULL,
    reminder_type ENUM('SMS', 'Email') NOT NULL,
    contact_info VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending'
);