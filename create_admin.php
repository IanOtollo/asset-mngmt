<?php
require_once __DIR__ . '/inc/db.php';

$username = 'admin';
$password = 'admin123';
$full_name = 'System Admin';
$role = 'admin';

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    echo "Admin user already exists.";
    exit;
}

$ins = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, role) VALUES (?, ?, ?, ?)");
if ($ins->execute([$username, $hash, $full_name, $role])) {
    echo "Admin created. Username: {$username} Password: {$password}. Delete this file now for security.";
} else {
    echo "Error creating admin.";
}
