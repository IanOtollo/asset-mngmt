<?php
require_once __DIR__.'/inc/db.php';
require_once __DIR__.'/inc/header.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM assets WHERE id = ?");
    $stmt->execute([$id]);

    $user_id = $_SESSION['user']['id'] ?? null;
    $aud = $pdo->prepare("INSERT INTO audit_logs (user_id, action, target_table, target_id, data) VALUES (?, 'delete', 'assets', ?, ?)");
    $aud->execute([$user_id, $id, json_encode(['deleted_id' => $id])]);
}

header('Location: index.php');
exit;

require_once __DIR__.'/inc/footer.php';
