<?php
// =============================================
//  config.php — Database Connection
//  Smart Student Portal | Adarsh Ray | BCA 2026
// =============================================
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');    
define('DB_NAME', 'college');

$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    '',
    8889,
    '/Applications/MAMP/tmp/mysql/mysql.sock'
);

if ($conn->connect_error) {
    die('<div style="
        font-family:sans-serif;
        background:#1a1448;
        color:#ff8fab;
        padding:40px;
        text-align:center;
        min-height:100vh;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-direction:column;
    ">
        <h2>⚠️ Database Connection Failed</h2>
        <p>' . $conn->connect_error . '</p>
        <p style="color:#a89ec9;margin-top:12px;font-size:0.85rem;">
            Make sure XAMPP/MAMP is running and the database "college" exists.
        </p>
    </div>');
}

// Create tables if they don't exist (auto-setup)
$conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
$conn->select_db(DB_NAME);

$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    course VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    course VARCHAR(50),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    subject VARCHAR(150),
    message TEXT,
    rating INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    filename VARCHAR(255),
    original_name VARCHAR(255),
    file_size INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Session helper
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php?msg=Please login to continue');
        exit();
    }
}

function getCurrentUser() {
    return [
        'id'        => $_SESSION['user_id']   ?? null,
        'username'  => $_SESSION['username']  ?? 'Guest',
        'full_name' => $_SESSION['full_name'] ?? 'Student',
        'course'    => $_SESSION['course']    ?? '',
    ];
}
