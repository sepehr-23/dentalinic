-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL, -- For plain text password initially, will be hashed later
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a default admin user
-- IMPORTANT: This stores the password as plain text.
-- This should be changed to use hashed passwords in a production environment.
INSERT INTO `users` (`username`, `password`) VALUES ('admin', 'password123');

-- You can add more sample users if needed, for example:
-- INSERT INTO `users` (`username`, `password`) VALUES ('testuser', 'testpass');
