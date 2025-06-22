<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Authenticate via email + password_verify()
 */
function login(string $email, string $pass)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($pass, $user['password'])) {
        return $user;
    }
    return false;
}

/** Redirect to login if not authenticated */
function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

/** Log out & return to login page */
function logout(): void
{
    session_destroy();
    header('Location: index.php');
    exit;
}
