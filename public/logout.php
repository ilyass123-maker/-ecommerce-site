<?php
// public/logout.php

session_start();
require_once __DIR__ . '/../includes/auth.php';

// Déconnecte l’utilisateur et redirige vers la page de connexion
logout();
