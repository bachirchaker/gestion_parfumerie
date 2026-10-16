<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM parfums WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: parfums.php');
exit;
?>
