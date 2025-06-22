<?php
// includes/auth.php

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Tente d’authentifier un utilisateur.
 * @param string $email
 * @param string $pass
 * @return array|false  Les données de l’utilisateur ou false si échec.
 */
function login(string $email, string $pass)
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND password = ?');
    $stmt->execute([$email, $pass]);
    return $stmt->fetch();
}

/**
 * Vérifie qu’un utilisateur est connecté, sinon redirige vers la page de login.
 */
function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Déconnecte l’utilisateur et retourne à la page de connexion.
 */
function logout(): void
{
    session_destroy();
    header('Location: index.php');
    exit;
}
