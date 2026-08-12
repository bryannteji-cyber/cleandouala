-- CleanDouala Database Schema
-- Run this in phpMyAdmin (XAMPP)

CREATE DATABASE IF NOT EXISTS cleandouala CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cleandouala;

-- Reports table (dumps, overflowing bins, clogged drains)
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('dump', 'overflowing_bin', 'clogged_drain', 'flood_risk', 'other') NOT NULL,
    description TEXT,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    status ENUM('pending', 'in_progress', 'resolved') DEFAULT 'pending',
    reporter_name VARCHAR(100) DEFAULT 'Anonymous',
    reporter_phone VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Pickup requests table
CREATE TABLE pickups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter_name VARCHAR(100) NOT NULL,
    reporter_phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8) DEFAULT NULL,
    longitude DECIMAL(11, 8) DEFAULT NULL,
    waste_type VARCHAR(100) DEFAULT 'Mixed household',
    quantity VARCHAR(50) DEFAULT 'Medium',
    preferred_date DATE DEFAULT NULL,
    preferred_time VARCHAR(50) DEFAULT NULL,
    notes TEXT,
    status ENUM('pending', 'accepted', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data so the map is not empty
INSERT INTO reports (type, description, latitude, longitude, reporter_name, status) VALUES
('dump', 'Big pile of plastic and household waste near the market', 4.0511, 9.7679, 'Jean', 'pending'),
('clogged_drain', 'Drain completely blocked after rain, water is rising', 4.0485, 9.7045, 'Marie', 'pending'),
('overflowing_bin', 'Public bin overflowing for 3 days', 4.0610, 9.7100, 'Paul', 'pending'),
('flood_risk', 'Water already flooding the street because of blocked drains', 4.0550, 9.7200, 'Amina', 'pending'),
('dump', 'Illegal dumping behind the school', 4.0400, 9.7500, 'Anonymous', 'in_progress');
