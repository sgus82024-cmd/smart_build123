<?php
require_once 'config.php';

// Проверка авторизации и прав администратора
if (!isLoggedIn() || !hasRole('admin')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID проекта не указан']);
    exit;
}

try {
    // Получаем информацию о проекте для удаления изображений
    $stmt = $pdo->prepare("SELECT before_image, after_image FROM projects WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $project = $stmt->fetch();
    
    // Удаляем изображения
    if ($project['before_image']) {
        @unlink('../' . $project['before_image']);
    }
    if ($project['after_image']) {
        @unlink('../' . $project['after_image']);
    }
    
    // Удаляем проект из БД
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}