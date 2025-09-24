<?php
require_once 'config.php';

// Проверка авторизации и прав администратора
if (!isLoggedIn() || !hasRole('admin')) {
    $_SESSION['error'] = 'У вас нет прав для выполнения этого действия';
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Получаем URL изображений из формы
        $beforeImage = !empty($_POST['before_image']) ? $_POST['before_image'] : null;
        $afterImage = !empty($_POST['after_image']) ? $_POST['after_image'] : null;
        
        $stmt = $pdo->prepare("
            INSERT INTO projects (title, description, before_image, after_image, service_id, completed_date, is_featured, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $_POST['title'],
            $_POST['description'] ?? null,
            $beforeImage,
            $afterImage,
            $_POST['service_id'] ?: null,
            $_POST['completed_date'] ?: null,
            $_POST['is_featured']
        ]);
        
        $_SESSION['success'] = 'Проект успешно добавлен';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Ошибка при добавлении проекта: ' . $e->getMessage();
    }
    
    header('Location: admin.php?tab=portfolio');
    exit;
}