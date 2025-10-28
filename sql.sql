CREATE TABLE IF NOT EXISTS attendance_[session_id] (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    program VARCHAR(20) NOT NULL,
    year_of_study INT NOT NULL,
    session_id VARCHAR(50) NOT NULL,
    course VARCHAR(20) NOT NULL,
    entry_time DATETIME NULL,
    exit_time DATETIME NULL,
    status ENUM('present', 'exited', 'absent') DEFAULT 'absent',
    entry_method ENUM('fingerprint', 'barcode') NULL,
    exit_method ENUM('fingerprint', 'barcode') NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);