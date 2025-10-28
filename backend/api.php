<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Database configuration
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "attend";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Start session for all requests
session_start();

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $department = trim($_POST['department']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($fullName) || empty($email) || empty($department) || empty($password)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid email format"]);
        exit();
    }
    
    if (strlen($password) < 8) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long"]);
        exit();
    }
    
    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM lecturers WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();
    
    if ($checkEmail->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["success" => false, "message" => "Email already registered"]);
        exit();
    }
    
    $checkEmail->close();
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO lecturers (full_name, email, department, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullName, $email, $department, $hashedPassword);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registration successful. You can now login."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Registration failed: " . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
    exit();
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Email and password are required"]);
        exit();
    }
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id, full_name, email, department, password FROM lecturers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Invalid email or password"]);
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        // Start session (you might want to use JWT for a more secure approach)
        $_SESSION['lecturer_id'] = $user['id'];
        $_SESSION['lecturer_name'] = $user['full_name'];
        $_SESSION['lecturer_email'] = $user['email'];
        $_SESSION['lecturer_department'] = $user['department'];
        
        echo json_encode([
            "success" => true, 
            "message" => "Login successful", 
            "user" => [
                "id" => $user['id'],
                "name" => $user['full_name'],
                "email" => $user['email'],
                "department" => $user['department']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Invalid email or password"]);
    }
    
    $stmt->close();
    $conn->close();
    exit();
}

// Handle logout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'logout') {
    // Destroy session
    session_destroy();
    echo json_encode(["success" => true, "message" => "Logout successful"]);
    exit();
}

// Handle get user info
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'getUserInfo') {
    // Check if user is logged in
    if (!isset($_SESSION['lecturer_id'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "User not logged in"]);
        exit();
    }
    
    echo json_encode([
        "success" => true,
        "user" => [
            "id" => $_SESSION['lecturer_id'],
            "name" => $_SESSION['lecturer_name'],
            "email" => $_SESSION['lecturer_email'],
            "department" => $_SESSION['lecturer_department']
        ]
    ]);
    exit();
}

// Handle session creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'createSession') {
    // Check if lecturer_id is provided (from Supabase auth)
    if (!isset($_POST['lecturer_id']) || empty($_POST['lecturer_id'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "User not logged in"]);
        exit();
    }
    
    $lecturer_id = $_POST['lecturer_id'];
    $course = trim($_POST['course']);
    $sessionTitle = trim($_POST['sessionTitle']);
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $location = trim($_POST['location']);
    $sessionType = trim($_POST['sessionType']);
    $sessionDescription = trim($_POST['sessionDescription']);
    
    // Validate input
    if (empty($course) || empty($sessionTitle) || empty($startTime) || empty($endTime) || empty($location)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Required fields are missing"]);
        exit();
    }
    
    // Validate time
    if (strtotime($startTime) >= strtotime($endTime)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "End time must be after start time"]);
        exit();
    }
    
    // Generate session ID
    $sessionId = 'S' . time() . rand(100, 999);
    
    // Insert session into database
    $stmt = $conn->prepare("INSERT INTO sessions (session_id, lecturer_id, course, session_title, start_time, end_time, location, session_type, description, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'scheduled', NOW())");
    $stmt->bind_param("sisssssss", $sessionId, $lecturer_id, $course, $sessionTitle, $startTime, $endTime, $location, $sessionType, $sessionDescription);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true, 
            "message" => "Session created successfully",
            "session" => [
                "id" => $sessionId,
                "course" => $course,
                "title" => $sessionTitle,
                "startTime" => $startTime,
                "endTime" => $endTime,
                "location" => $location,
                "status" => "scheduled"
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to create session: " . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
    exit();
}

// Handle get sessions for lecturer
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'getSessions') {
    // Check if lecturer_id is provided (from Supabase auth)
    if (!isset($_GET['lecturer_id']) || empty($_GET['lecturer_id'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "User not logged in"]);
        exit();
    }
    
    $lecturer_id = $_GET['lecturer_id'];
    
    // Get sessions for the lecturer
    $stmt = $conn->prepare("SELECT session_id, course, session_title, start_time, end_time, location, session_type, description, status, created_at FROM sessions WHERE lecturer_id = ? ORDER BY start_time DESC");
    $stmt->bind_param("i", $lecturer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sessions = [];
    while ($row = $result->fetch_assoc()) {
        $sessions[] = [
            "id" => $row['session_id'],
            "course" => $row['course'],
            "title" => $row['session_title'],
            "startTime" => $row['start_time'],
            "endTime" => $row['end_time'],
            "location" => $row['location'],
            "type" => $row['session_type'],
            "description" => $row['description'],
            "status" => $row['status'],
            "createdAt" => $row['created_at']
        ];
    }
    
    echo json_encode([
        "success" => true,
        "sessions" => $sessions
    ]);
    
    $stmt->close();
    $conn->close();
    exit();
}

// Handle session status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'updateSessionStatus') {
    // Check if lecturer_id is provided (from Supabase auth)
    if (!isset($_POST['lecturer_id']) || empty($_POST['lecturer_id'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "User not logged in"]);
        exit();
    }
    
    $lecturer_id = $_POST['lecturer_id'];
    $sessionId = $_POST['sessionId'];
    $status = $_POST['status'];
    
    // Validate status
    $validStatuses = ['scheduled', 'active', 'completed', 'cancelled'];
    if (!in_array($status, $validStatuses)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid status"]);
        exit();
    }
    
    // Update session status
    $stmt = $conn->prepare("UPDATE sessions SET status = ? WHERE session_id = ? AND lecturer_id = ?");
    $stmt->bind_param("ssi", $status, $sessionId, $lecturer_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Session status updated successfully"
        ]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Session not found or no changes made"]);
    }
    
    $stmt->close();
    $conn->close();
    exit();
}

// Handle session deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'deleteSession') {
    // Check if lecturer_id is provided (from Supabase auth)
    if (!isset($_POST['lecturer_id']) || empty($_POST['lecturer_id'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "User not logged in"]);
        exit();
    }
    
    $lecturer_id = $_POST['lecturer_id'];
    $sessionId = $_POST['sessionId'];
    
    // Delete session from database
    $stmt = $conn->prepare("DELETE FROM sessions WHERE session_id = ? AND lecturer_id = ?");
    $stmt->bind_param("si", $sessionId, $lecturer_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Session deleted successfully"
        ]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Session not found or you don't have permission to delete it"]);
    }
    
    $stmt->close();
    $conn->close();
    exit();
}

// Invalid request
http_response_code(400);
echo json_encode(["success" => false, "message" => "Invalid request"]);
?>