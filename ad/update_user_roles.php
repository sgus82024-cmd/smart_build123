<?php
require_once 'config.php';

// Проверка авторизации и прав администратора
if (!isLoggedIn() || !hasRole('admin')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit;
}

// Получаем данные из запроса
$user_id = $_POST['user_id'] ?? 0;
$role_id = $_POST['role_id'] ?? 0;
$action = $_POST['action'] ?? '';

// Валидация данных
if (!$user_id || !$role_id || !in_array($action, ['add', 'remove'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Неверные параметры запроса']);
    exit;
}

try {
    if ($action === 'add') {
        // Добавляем роль
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $role_id]);
    } else {
        // Удаляем роль
        $stmt = $pdo->prepare("DELETE FROM user_roles WHERE user_id = ? AND role_id = ?");
        $stmt->execute([$user_id, $role_id]);
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()]);
}