<?php
require_once 'config.php';

// Проверка авторизации и прав менеджера
if (!isLoggedIn() || !hasRole('manager')) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['status'])) {
    try {
        $pdo->beginTransaction();
        
        foreach ($_POST['status'] as $request_id => $status) {
            // Проверка, что статус допустимый
            if (!in_array($status, ['new', 'in_progress', 'completed', 'canceled'])) {
                continue;
            }
            
            // Обновление статуса заявки
            $stmt = $pdo->prepare("UPDATE repair_requests SET status = ? WHERE id = ?");
            $stmt->execute([$status, $request_id]);
        }
        
        $pdo->commit();
        $_SESSION['success_message'] = 'Статусы заявок успешно обновлены';
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = 'Ошибка при обновлении статусов: ' . $e->getMessage();
    }
}

redirect('manager.php?tab=requests');
?>