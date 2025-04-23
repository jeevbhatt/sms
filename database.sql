-- Create database
CREATE DATABASE IF NOT EXISTS school_management;
USE school_management;

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    semester INT NOT NULL,
    rollno VARCHAR(20) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    b_ed VARCHAR(50) NOT NULL,
    admitted_year VARCHAR(4) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL
);

-- Create notices table
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL
);

-- Create contact_messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL
);

-- Create admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL
);

-- Insert default admin user
INSERT INTO admin (username, password, created_at)
VALUES ('admin', '$2y$10$8KzO3LOgpxRBUEZ8pu9jEOUVl1xOvkUZe9gqUwGKoQRJUZvKgXJxm', NOW());

-- Insert sample notices
INSERT INTO notices (title, content, created_at) VALUES
('School Reopening Date', 'The school will reopen on September 1, 2024 after the summer break. All students are expected to be present in their respective classrooms by 8:00 AM.', NOW()),
('Annual Sports Day', 'The annual sports day will be held on October 15, 2024. Students interested in participating should register with their physical education teacher by September 30.', NOW()),
('Parent-Teacher Meeting', 'A parent-teacher meeting is scheduled for September 10, 2024 from 9:00 AM to 1:00 PM. Parents are requested to attend to discuss their child''s academic progress.', NOW());
