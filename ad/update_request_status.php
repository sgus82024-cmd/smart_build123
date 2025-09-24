<?php
require_once 'config.php';

if (!isLoggedIn() || !hasRole('admin')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit;
}

$request_id = $_POST['id'] ?? 0;
$new_status = $_POST['status'] ?? '';

// Проверка валидности статуса
$allowed_statuses = ['new', 'in_progress', 'completed', 'canceled'];
if (!in_array($new_status, $allowed_statuses)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Неверный статус']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE repair_requests SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$new_status, $request_id]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}