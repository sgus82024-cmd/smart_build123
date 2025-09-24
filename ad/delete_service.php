<?php
require_once 'config.php';

// Проверка авторизации и прав администратора
if (!isLoggedIn() || !hasRole('admin')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Проверка ID
if (!isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID услуги не указан']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Удаляем связи с категориями
    $stmt = $pdo->prepare("DELETE FROM service_to_category WHERE service_id = ?");
    $stmt->execute([$_GET['id']]);
    
    // Удаляем саму услугу
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    
    $pdo->commit();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}