<?php
function register_user($pdo, $name, $email, $password){
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
    return $stmt->execute([$name, $email, $hash, 'user']);
}
