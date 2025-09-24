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
        $stmt = $pdo->prepare("
            INSERT INTO promotions (title, description, discount_value, start_date, end_date, image, is_active, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $_POST['title'],
            $_POST['description'] ?? null,
            $_POST['discount_value'],
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['image'] ?? null,
            $_POST['is_active']
        ]);
        
        $_SESSION['success'] = 'Акция успешно добавлена';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Ошибка при добавлении акции: ' . $e->getMessage();
    }
    
    header('Location: admin.php?tab=promotions');
    exit;
}