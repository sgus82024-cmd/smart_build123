<?php
require_once 'config.php';

// Проверка авторизации и прав администратора
if (!isLoggedIn() || !hasRole('admin')) {
    header('Location: login.php');
    exit();
}

// Проверка наличия ID отзыва
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'Неверный ID отзыва';
    header('Location: admin.php?tab=reviews');
    exit();
}

$review_id = (int)$_GET['id'];

try {
    // Получение текущего статуса отзыва
    $stmt = $pdo->prepare("SELECT is_approved FROM reviews WHERE id = ?");
    $stmt->execute([$review_id]);
    $review = $stmt->fetch();

    if (!$review) {
        $_SESSION['error'] = 'Отзыв не найден';
        header('Location: admin.php?tab=reviews');
        exit();
    }

    // Инвертирование статуса одобрения (используем булево значение)
    $new_status = $review['is_approved'] ? false : true;

    // Обновление статуса отзыва
    $stmt = $pdo->prepare("UPDATE reviews SET is_approved = ? WHERE id = ?");
    $stmt->execute([$new_status, $review_id]);

    // Логирование для отладки
    error_log("Review ID: $review_id, New status: " . ($new_status ? 'approved' : 'not approved'));

    $_SESSION['success'] = 'Статус отзыва успешно обновлен';
} catch (PDOException $e) {
    error_log("Error approving review: " . $e->getMessage());
    $_SESSION['error'] = 'Ошибка при обновлении отзыва';
}

header('Location: admin.php?tab=reviews');
exit();
?>