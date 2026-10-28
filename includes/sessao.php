<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['tecnico_id'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
require_once __DIR__ . '/csrf.php';
