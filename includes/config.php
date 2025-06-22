<?php
// includes/config.php

// Paramètres de connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'ehtp_gi');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    // Initialisation de la connexion PDO
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    // En cas d’erreur, arrêt et affichage du message
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
