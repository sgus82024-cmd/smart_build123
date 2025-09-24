<?php
require_once 'config.php';

// Проверка авторизации и прав администратора
if (!isLoggedIn() || !hasRole('admin')) {
    redirect('login.php');
}

// Проверка наличия ID отзыва
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'Неверный ID отзыва';
    redirect('admin.php?tab=reviews');
}

$review_id = (int)$_GET['id'];

// Удаление отзыва
$stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
$stmt->execute([$review_id]);

$_SESSION['success'] = 'Отзыв успешно удален';
redirect('admin.php?tab=reviews');
?>