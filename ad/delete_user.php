<?php
require_once 'config.php';

// Проверка авторизации и прав администратора
if (!isLoggedIn() || !hasRole('admin')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit;
}

// Получение ID пользователя для удаления
$user_id = $_GET['id'] ?? 0;
if (!$user_id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Не указан ID пользователя']);
    exit;
}

// Проверка, что пользователь не удаляет сам себя
if ($user_id == $_SESSION['user_id']) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Вы не можете удалить свой собственный аккаунт']);
    exit;
}

try {
    // Начинаем транзакцию
    $pdo->beginTransaction();
    
    // Удаляем роли пользователя
    $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?")->execute([$user_id]);
    
    // Удаляем отзывы пользователя
    $pdo->prepare("DELETE FROM reviews WHERE user_id = ?")->execute([$user_id]);
    
    // Обновляем заявки (устанавливаем user_id в NULL)
    $pdo->prepare("UPDATE repair_requests SET user_id = NULL WHERE user_id = ?")->execute([$user_id]);
    
    // Удаляем самого пользователя
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
    
    // Подтверждаем транзакцию
    $pdo->commit();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Откатываем транзакцию в случае ошибки
    $pdo->rollBack();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Ошибка при удалении пользователя: ' . $e->getMessage()]);
}