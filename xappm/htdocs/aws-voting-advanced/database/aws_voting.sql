
CREATE DATABASE IF NOT EXISTS aws_voting;
USE aws_voting;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    has_voted TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE elections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    start_date DATETIME,
    end_date DATETIME,
    status ENUM('active','inactive') DEFAULT 'inactive'
);

CREATE TABLE candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    election_id INT,
    name VARCHAR(100),
    position VARCHAR(100)
);

CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    candidate_id INT,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (username,password) VALUES ('admin','admin123');
INSERT INTO elections (title,start_date,end_date,status) VALUES 
('College Election 2026','2026-02-01 09:00:00','2026-12-31 18:00:00','active');
INSERT INTO candidates (election_id,name,position) VALUES
(1,'Candidate A','President'),
(1,'Candidate B','President'),
(1,'Candidate C','Vice President');
