<?php
require_once 'config.php';

// Проверка авторизации и прав менеджера
if (!isLoggedIn() || !hasRole('manager')) {
    redirect('login.php');
}

// Получение информации о текущем пользователе
$stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Определение активной вкладки
$tab = $_GET['tab'] ?? 'requests';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Менеджер-панель - Smart Build</title>
    <link rel="shortcut icon" href="..\assets\images\logo.ico" type="image/x-icon">
    <style>
           
       @import url('https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Itim&family=M+PLUS+Rounded+1c&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Russo+One&family=Shafarik&display=swap');
.roboto-tree {
    font-family: "Roboto", sans-serif;
    font-optical-sizing: auto;
    font-weight: light;
    font-style: normal;
    font-variation-settings:
      "wdth" 100;
  }
  .inter-ttt {
    font-family: "Inter", sans-serif;
    font-optical-sizing: auto;
    font-weight: bold;
    font-style: normal;
  }
/* Основные стили */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

body {
    background: rgba(122, 30, 76, 0.05);
    color: #333;
    line-height: 1.6;
}

/* Контейнер админ-панели */
.manager-container {
    display: flex;
    min-height: 100vh;
}
.welcome-message {
    margin-bottom: 50px;
}
select.status-select {
    border: none;
}
/* Сайдбар */
.sidebar {
    width: 294px;
    background-color: #7A1E4C;
    padding: 25px 20px;
    box-shadow: 0px 0px 32px rgba(0, 0, 0, 0.24);
    color: white;
}

.sidebar-header h2 {
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    font-size: 20px;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
}

.sidebar-menu {
    list-style: none;
    margin-bottom: 30px;
}

.sidebar-menu li {
    margin-bottom: 5px;
}

.sidebar-menu a {
    display: block;
    padding: 12px 15px;
    text-decoration: none;
    color: rgba(255, 255, 255, 0.8);
    border-radius: 4px;
    transition: all 0.3s;
    font-family: 'Inter', sans-serif;
    font-size: 14px;
}

.sidebar-menu a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
}

.sidebar-menu a.active {
    background-color: white;
    color: #7A1E4C;
    font-weight: 500;
}

/* Блок пользователя */
.user-info {
    margin-top: auto;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-info p {
    margin-bottom: 8px;
    color: white;
}

.user-info a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color 0.3s;
}

.user-info a:hover {
    color: white;
}

/* Основное содержимое */
.main-content {
    flex: 1;
    padding: 30px;
    background-color: #f8f9fa;
}

/* Хедер */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.page-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
}

.close-btn {
    font-size: 30px;
    color: #7A1E4C;
    text-decoration: none;
    transition: color 0.3s;
}

.close-btn:hover {
    color: #5a1644;
}

/* Карточки */
.card {
    background-color: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
}

.card h2 {
    font-size: 20px;
    margin-bottom: 20px;
    color: #333;
}

/* Таблицы */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

table th {
    background-color: #f5f5f5;
    font-weight: 500;
    color: #555;
}

/* Кнопки */
.btn {
    display: inline-block;
    padding: 8px 15px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s;
    margin-right: 5px;
}

.btn-primary {
   
        color: #7A1E4C;
    
}
button.btn.btn-success{
    display: flex
;
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
    width: 200px;
    height: 40px;
    text-align: center;
    align-items: center;
    justify-content: center;
}
button.btn.btn-success:hover{
 color: #7A1E4C;
    transform: translateY(-2px); 
background:transparent;
}
.btn-primary:hover {
    background-color: #7A1E4C;
    border-color:#7A1E4C;
    color:white;
}

.btn-edit {
    background-color: #4a8bf0;
    color: white;
    border: 1px solid #4a8bf0;
}

.btn-edit:hover {
    background-color: #3a7be0;
    border-color: #3a7be0;
}

.btn-danger {
    background-color: #e74c3c;
    color: white;
    border: 1px solid #e74c3c;
}

.btn-danger:hover {
    background-color: #c0392b;
    border-color: #c0392b;
}

/* Статусы */
.status {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    color: white;
}

.status-new {
    background-color: #ff9800;
}

.status-in_progress {
    background-color: #2196f3;
}

.status-completed {
    background-color: #4caf50;
}

.status-canceled {
    background-color: #f44336;
}

/* Вкладки */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Крестик закрытия */
.close-icon {
    display: inline-block;
    width: 20px;
    height: 20px;
    position: relative;
    cursor: pointer;
}

.close-icon:before, .close-icon:after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 2px;
    background-color: #7A1E4C;
}

.close-icon:before {
    transform: translate(-50%, -50%) rotate(45deg);
}

.close-icon:after {
    transform: translate(-50%, -50%) rotate(-45deg);
}

/* Адаптивность */
@media (max-width: 992px) {
    .manager-container {
        flex-direction: column;
    }
    
    .sidebar {
        width: 100%;
    }
    
    .main-content {
        padding: 20px;
    }
}
@media (max-width: 768px) {
    .card {
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    
    .card h2 {
        font-size: 18px;
        margin-bottom: 15px;
    }
    
    table {
        display: block;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    table th, table td {
        padding: 8px 10px;
        font-size: 14px;
        white-space: nowrap;
    }
}

@media (max-width: 480px) {
    .card {
        padding: 12px;
    }
    
    .card h2 {
        font-size: 16px;
    }
    
    table th, table td {
        padding: 6px 8px;
        font-size: 13px;
    }
}
</style>
</head>
<body>
<div class="user-info" style="position: relative;">
  <p style="text-align: right; margin: 0; padding: 0;">
    <a href="../index.php" style="display: inline-block; padding: 5px 10px;  text-decoration: none; color: #333;">×</a>
  </p>
</div>
    <div class="manager-container">
        <!-- Боковая панель -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Менеджер-панель</h2>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="?tab=requests" class="<?= $tab === 'requests' ? 'active' : '' ?>">Управление заявками</a></li>
                <li><a href="?tab=reviews" class="<?= $tab === 'reviews' ? 'active' : '' ?>">Просмотр отзывов</a></li>
            </ul>
            
            <div class="user-info">
                <p><strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong></p>
                <p><?= htmlspecialchars($user['email']) ?></p>
                <p><a href="logout.php" style="color: #c2c7d0;">Выйти</a></p>
            </div>
        </div>
        
        <!-- Основное содержимое -->
        <div class="main-content">
            <div class="header">
                <h1>
                    <?php 
                    switch($tab) {
                        case 'reviews': echo 'Просмотр отзывов'; break;
                        default: echo 'Управление заявками';
                    }
                    ?>
                </h1>
            </div>
            
            <div class="welcome-message">
                <h2>Приветствуем вас, уважаемый менеджер!</h2>
                <p>Вы находитесь в менеджер-панели Smart Build. Здесь вы можете управлять заявками и просматривать отзывы.</p>
            </div>
            
            <!-- Контент для вкладки "Заявки" -->
            <div class="tab-content <?= $tab === 'requests' ? 'active' : '' ?>">
                <div class="card">
                    <h2>Управление заявками</h2>
                    
                    <?php
                    // Получение списка заявок
                    $requests = $pdo->query("
                        SELECT r.id, u.first_name, u.last_name, u.email, s.title as service, 
                               r.address, r.status, r.created_at, r.updated_at, r.description
                        FROM repair_requests r
                        LEFT JOIN users u ON r.user_id = u.id
                        LEFT JOIN services s ON r.service_id = s.id
                        ORDER BY 
                            CASE r.status
                                WHEN 'new' THEN 1
                                WHEN 'in_progress' THEN 2
                                WHEN 'completed' THEN 3
                                WHEN 'canceled' THEN 4
                                ELSE 5
                            END,
                            r.created_at DESC
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <form method="post" action="update_requests.php">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Клиент</th>
                                    <th>Email</th>
                                    <th>Услуга</th>
                                    <th>Адрес</th>
                                    <th>Статус</th>
                                    <th>Дата создания</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($request['id']) ?></td>
                                        <td><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></td>
                                        <td><?= htmlspecialchars($request['email']) ?></td>
                                        <td><?= htmlspecialchars($request['service']) ?></td>
                                        <td><?= htmlspecialchars($request['address']) ?></td>
                                        <td>
                                            <select name="status[<?= $request['id'] ?>]" class="status-select">
                                                <option value="new" <?= $request['status'] === 'new' ? 'selected' : '' ?>>Новая</option>
                                                <option value="in_progress" <?= $request['status'] === 'in_progress' ? 'selected' : '' ?>>В работе</option>
                                                <option value="completed" <?= $request['status'] === 'completed' ? 'selected' : '' ?>>Завершена</option>
                                                <option value="canceled" <?= $request['status'] === 'canceled' ? 'selected' : '' ?>>Отменена</option>
                                            </select>
                                        </td>
                                        <td><?= date('d.m.Y H:i', strtotime($request['created_at'])) ?></td>
                                        <td>
                                            <a href="view_request.php?id=<?= $request['id'] ?>" class="btn btn-primary">Просмотр</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn btn-success">Сохранить изменения</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Контент для вкладки "Отзывы" -->
            <div class="tab-content <?= $tab === 'reviews' ? 'active' : '' ?>">
                <div class="card">
                    <h2>Просмотр отзывов</h2>
                    
                    <?php
                    // Получение списка отзывов
                    $reviews = $pdo->query("
                        SELECT r.id, r.rating, r.comment, r.is_approved, r.created_at,
                               u.first_name, u.last_name, u.email,
                               req.id as request_id, s.title as service
                        FROM reviews r
                        LEFT JOIN users u ON r.user_id = u.id
                        LEFT JOIN repair_requests req ON r.request_id = req.id
                        LEFT JOIN services s ON req.service_id = s.id
                        ORDER BY r.created_at DESC
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Пользователь</th>
                                <th>Заявка</th>
                                <th>Рейтинг</th>
                                <th>Комментарий</th>
                                <th>Дата</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td><?= htmlspecialchars($review['id']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($review['first_name'] . ' ' . $review['last_name']) ?><br>
                                        <?= htmlspecialchars($review['email']) ?>
                                    </td>
                                    <td>
                                        <?php if ($review['request_id']): ?>
                                            #<?= $review['request_id'] ?> (<?= htmlspecialchars($review['service']) ?>)
                                        <?php else: ?>
                                            Общий отзыв
                                        <?php endif; ?>
                                    </td>
                                    <td><?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?></td>
                                    <td><?= htmlspecialchars($review['comment']) ?></td>
                                    <td><?= date('d.m.Y', strtotime($review['created_at'])) ?></td>
                                    <td><?= $review['is_approved'] ? 'Одобрен' : 'На модерации' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>