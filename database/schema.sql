-- Campus Events database
-- Import this file in phpMyAdmin after creating a database.

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user'
);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    category ENUM('Seminar','Workshop','Sports','Social') NOT NULL,
    event_date DATETIME NOT NULL,
    venue VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    description TEXT
);

CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attended TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE (user_id, event_id),   -- a user can register only once per event
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- passwords are hashed (admin123 and user123)
INSERT INTO users (username, full_name, password, role) VALUES
('admin', 'Event Organizer', '$2y$12$LRo2Esfd9MzrsBbkPRMWzesgLZyHBN/bBD/vDySIdbsBUPbIkKUze', 'admin'),
('juan',  'Juan Dela Cruz',  '$2y$12$6UccfWGz17TXyVNkECTLseIwc9Imt0NFEHfQs1OQMZ3zbkOI2ocKG', 'user'),
('maria', 'Maria Santos',    '$2y$12$6UccfWGz17TXyVNkECTLseIwc9Imt0NFEHfQs1OQMZ3zbkOI2ocKG', 'user');

INSERT INTO events (title, category, event_date, venue, capacity, description) VALUES
('Intro to Web Development Seminar', 'Seminar',  TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 5 DAY),  '09:00:00'), 'Computer Lab 1', 50,  'Learn the basics of HTML, CSS, JavaScript and PHP from our senior students. Bring your own laptop.'),
('Resume Writing Workshop',         'Workshop', TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 8 DAY),  '13:30:00'), 'Room 204',        30,  'Get tips on how to write a good resume and prepare for job interviews.'),
('Leadership Training (Limited)',   'Workshop', TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 10 DAY), '10:00:00'), 'Conference Hall', 2,   'A small group training for student leaders. Only 2 slots!'),
('Inter-Department Basketball',     'Sports',   TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 14 DAY), '15:00:00'), 'University Gym',   100, 'Come and cheer for your department or join a team.'),
('Acquaintance Party',              'Social',   TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 20 DAY), '18:00:00'), 'Open Grounds',     200, 'Meet new friends, enjoy food, music and games.'),
('Orientation Day (Past Event)',    'Seminar',  TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 10 DAY), '08:00:00'), 'Main Auditorium',  300, 'Welcome orientation for all new students.');

INSERT INTO registrations (user_id, event_id, attended) VALUES
(2, 1, 0),
(3, 1, 0),
(2, 3, 0),
(3, 3, 0),
(2, 6, 1);
