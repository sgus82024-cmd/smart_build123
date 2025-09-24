<?php
require_once 'config.php';

// Проверка авторизации и прав администратора
if (!isLoggedIn() || !hasRole('admin')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Проверка данных
if (!isset($_POST['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID услуги не указан']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE services SET is_active = ? WHERE id = ?");
    $stmt->execute([$_POST['is_active'], $_POST['id']]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}