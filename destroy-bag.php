<?php
session_start();
require_once 'connection.php';

if (empty($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

$ref = $_REQUEST['ref'] ?? '';
if ($ref !== '') {
    $stmt = $connection->prepare('DELETE FROM pannier WHERE mail_login = :login AND reference = :ref');
    $stmt->execute([':login' => $_SESSION['login'], ':ref' => $ref]);
}

header('Location: cart.php');
exit;
?>