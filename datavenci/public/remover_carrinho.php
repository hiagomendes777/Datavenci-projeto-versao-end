<?php
require_once __DIR__ . '/../includes/functions.php';
start_secure_session();

if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit;
}

$id = $_GET['id'] ?? null;

if ($id && isset($_SESSION['carrinho'][$id])) {
  unset($_SESSION['carrinho'][$id]);
}

header('Location: carrinho.php');
exit;
