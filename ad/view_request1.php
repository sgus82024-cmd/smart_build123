<?php
require_once 'config.php';

// Проверка авторизации и прав администратора
if (!isLoggedIn() || !hasRole('admin')) {
    redirect('login.php');
}

$request_id = $_GET['id'] ?? 0;

// Обработка изменения статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE repair_requests SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$new_status, $request_id]);
    
    $_SESSION['success_message'] = 'Статус заявки успешно обновлен';
    redirect("view_request1.php?id=$request_id");
}

// Получение информации о заявке
$stmt = $pdo->prepare("
    SELECT r.*, u.first_name, u.last_name, u.email, u.phone,
           s.title as service_title, s.description as service_description,
           p.title as promotion_title, p.discount_value
    FROM repair_requests r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN services s ON r.service_id = s.id
    LEFT JOIN promotions p ON r.promotion_id = p.id
    WHERE r.id = ?
");
$stmt->execute([$request_id]);
$request = $stmt->fetch();

if (!$request) {
    $_SESSION['error_message'] = 'Заявка не найдена';
    redirect('admin.php?tab=requests');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр заявки #<?= $request_id ?> - Smart Build</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Itim&family=M+PLUS+Rounded+1c&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Russo+One&family=Shafarik&display=swap');
        
        .roboto-tree {
            font-family: "Roboto", sans-serif;
            font-optical-sizing: auto;
            font-weight: light;
            font-style: normal;
            font-variation-settings: "wdth" 100;
        }
        
        .inter-ttt {
            font-family: "Inter", sans-serif;
            font-optical-sizing: auto;
            font-weight: bold;
            font-style: normal;
        }
        
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-top: 0; }
        .info-block { margin-bottom: 20px; }
        .info-block h2 { margin-top: 0; color: #7A1E4C; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .info-row { display: flex; margin-bottom: 10px; }
        .info-label { width: 200px; font-weight: bold; color: #666; }
        .info-value { flex: 1; }
        
        .back-link {
            display: flex;
            padding: 0px;
            background-color: #7A1E4C;
            color: white;
            text-decoration: none;
            border: solid;
            border-radius: 4px;
            font-weight: 1500;
            transition: background-color 0.3s;
            border-width: 2px;
            border-color: #7A1E4C;
            cursor: pointer;
            transition: all 0.3s;
            width: 240px;
            height: 40px;
            text-align: center;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin-top: 20px;
        }
        
        .back-link:hover {
            color: #7A1E4C;
            transform: translateY(-2px); 
            background:transparent;
        }
        
        .status-badge {
            display: inline-block; 
            padding: 5px 10px; 
            border-radius: 20px; 
            font-size: 14px; 
            font-weight: bold;
        }
        
        .status-new { background-color: #7A1E4C; color: white; }
        .status-in_progress { background-color: #17a2b8; color: white; }
        .status-completed { background-color: #28a745; color: white; }
        .status-canceled { background-color: #dc3545; color: white; }
        
        .status-form {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .status-form select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-right: 10px;
        }
        
        .status-form button {
            padding: 8px 15px;
            background-color: #7A1E4C;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .status-form button:hover {
            background-color: #5a1644;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Заявка #<?= $request_id ?></h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="color: green; margin-bottom: 15px;"><?= $_SESSION['success_message'] ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <div class="info-block">
            <h2>Основная информация</h2>
            <div class="info-row">
                <div class="info-label">Статус:</div>
                <div class="info-value">
                    <span class="status-badge status-<?= $request['status'] ?>">
                        <?php 
                        switch($request['status']) {
                            case 'new': echo 'Новая'; break;
                            case 'in_progress': echo 'В работе'; break;
                            case 'completed': echo 'Завершена'; break;
                            case 'canceled': echo 'Отменена'; break;
                            default: echo $request['status'];
                        }
                        ?>
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Дата создания:</div>
                <div class="info-value"><?= date('d.m.Y H:i', strtotime($request['created_at'])) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Последнее обновление:</div>
                <div class="info-value"><?= date('d.m.Y H:i', strtotime($request['updated_at'])) ?></div>
            </div>
            
            <!-- Форма изменения статуса -->
            <form class="status-form" method="POST">
                <label for="status">Изменить статус:</label>
                <select name="status" id="status">
                    <option value="new" <?= $request['status'] == 'new' ? 'selected' : '' ?>>Новая</option>
                    <option value="in_progress" <?= $request['status'] == 'in_progress' ? 'selected' : '' ?>>В работе</option>
                    <option value="completed" <?= $request['status'] == 'completed' ? 'selected' : '' ?>>Завершена</option>
                    <option value="canceled" <?= $request['status'] == 'canceled' ? 'selected' : '' ?>>Отменена</option>
                </select>
                <button type="submit">Обновить</button>
            </form>
        </div>
        
        <!-- Остальная часть формы (как в оригинальном view_request.php) -->
        <div class="info-block">
            <h2>Информация о клиенте</h2>
            <div class="info-row">
                <div class="info-label">ФИО:</div>
                <div class="info-value"><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value"><?= htmlspecialchars($request['email']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Телефон:</div>
                <div class="info-value"><?= htmlspecialchars($request['phone']) ?></div>
            </div>
        </div>
        
        <div class="info-block">
            <h2>Информация о заказе</h2>
            <div class="info-row">
                <div class="info-label">Услуга:</div>
                <div class="info-value">
                    <?= htmlspecialchars($request['service_title']) ?><br>
                    <small><?= htmlspecialchars($request['service_description']) ?></small>
                </div>
            </div>
            <?php if ($request['promotion_title']): ?>
            <div class="info-row">
                <div class="info-label">Акция:</div>
                <div class="info-value">
                    <?= htmlspecialchars($request['promotion_title']) ?> (скидка <?= $request['discount_value'] ?>%)
                </div>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <div class="info-label">Адрес:</div>
                <div class="info-value"><?= htmlspecialchars($request['address']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Площадь:</div>
                <div class="info-value"><?= $request['square_meters'] ? $request['square_meters'] . ' кв.м' : 'Не указана' ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Описание:</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($request['description'])) ?></div>
            </div>
        </div>
        
        <a href="admin.php?tab=requests" class="back-link">Вернуться к списку заявок</a>
    </div>
</body>
</html>