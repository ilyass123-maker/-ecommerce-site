<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Tente d’authentifier un utilisateur en vérifiant
 * le mot de passe via password_verify().
 */
function login(string $email, string $pass)
{
    global $pdo;
    // 1) Récupérer l’utilisateur par email
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2) Si trouvé, vérifier le mot de passe
    if ($user && password_verify($pass, $user['password'])) {
        return $user;
    }

    return false;
}

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

function logout(): void
{
    session_destroy();
    header('Location: index.php');
    exit;
}
