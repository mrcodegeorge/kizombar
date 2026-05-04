<?php
$hash = '$2y$10$Z3U6v2uR.T6iU4k5m9zNveYy3qHhWjL6pX4C5y.D1v0Xm6f8gR2yG';
$password = 'password123';
if (password_verify($password, $hash)) {
    echo "Hash matches password123\n";
} else {
    echo "Hash DOES NOT match password123\n";
}

// Generate a valid hash just in case
echo "New hash for password123: " . password_hash('password123', PASSWORD_DEFAULT) . "\n";
