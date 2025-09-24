<?php
require_once 'config.php';

// Проверка авторизации и прав администратора
if (!isLoggedIn() || !hasRole('admin')) {
    $_SESSION['error'] = 'У вас нет прав для выполнения этого действия';
    header('Location: login.php');
    exit;
}

// Обработка формы добавления услуги
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Добавляем основную информацию об услуге
        $stmt = $pdo->prepare("
            INSERT INTO services (title, description, price, duration, is_active, image, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $_POST['title'],
            $_POST['description'] ?? null,
            $_POST['price'],
            $_POST['duration'],
            $_POST['is_active'],
            $_POST['image'] ?? null
        ]);
        
        $service_id = $pdo->lastInsertId();
        
        // Добавляем категории для услуги
        if (!empty($_POST['categories'])) {
            $stmt = $pdo->prepare("INSERT INTO service_to_category (service_id, category_id) VALUES (?, ?)");
            
            foreach ($_POST['categories'] as $category_id) {
                $stmt->execute([$service_id, $category_id]);
            }
        }
        
        $pdo->commit();
        $_SESSION['success'] = 'Услуга успешно добавлена';
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Ошибка при добавлении услуги: ' . $e->getMessage();
    }
    
    header('Location: admin.php?tab=services');
    exit;
}