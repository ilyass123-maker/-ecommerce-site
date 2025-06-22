<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



 
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
