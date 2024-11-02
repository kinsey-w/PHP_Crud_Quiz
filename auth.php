<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}