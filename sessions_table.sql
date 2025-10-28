-- Create sessions table for MUBAS Attendance System
-- This table stores all session information created by lecturers

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(50) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `course` varchar(20) NOT NULL,
  `session_title` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `location` varchar(100) NOT NULL,
  `session_type` enum('lecture','lab','tutorial','workshop') DEFAULT 'lecture',
  `description` text,
  `status` enum('scheduled','active','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `lecturer_id` (`lecturer_id`),
  KEY `course` (`course`),
  KEY `status` (`status`),
  KEY `start_time` (`start_time`),
  CONSTRAINT `fk_sessions_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data (optional)
INSERT INTO `sessions` (`session_id`, `lecturer_id`, `course`, `session_title`, `start_time`, `end_time`, `location`, `session_type`, `description`, `status`) VALUES
('S1703123456789', 1, 'COE321', 'Processor Architecture Lecture', '2025-06-18 10:00:00', '2025-06-18 11:30:00', 'Control Lab', 'lecture', 'Introduction to processor architecture and design principles', 'active'),
('S1703123456790', 1, 'DSP401', 'Filter Design Lab', '2025-06-19 14:00:00', '2025-06-19 16:00:00', 'E224', 'lab', 'Hands-on filter design using MATLAB', 'scheduled'),
('S1703123456791', 1, 'LE301', 'Amplifier Circuits Tutorial', '2025-06-17 13:00:00', '2025-06-17 14:30:00', 'E225', 'tutorial', 'Problem solving session on amplifier circuits', 'completed');
