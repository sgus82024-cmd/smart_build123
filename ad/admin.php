<?php
require_once 'config.php';

// Проверка авторизации и прав администратора
if (!isLoggedIn() || !hasRole('admin')) {
    redirect('login.php');
}


// Получение информации о текущем пользователе
$stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();


// Отображение сообщений об успехе/ошибке
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

// Определение активной вкладки
$tab = $_GET['tab'] ?? 'dashboard';

// Получение списка проектов портфолио
$projects = $pdo->query("
    SELECT p.*, s.title as service_title 
    FROM projects p
    LEFT JOIN services s ON p.service_id = s.id
    ORDER BY p.completed_date DESC
")->fetchAll(PDO::FETCH_ASSOC);
// Получение списка акций
$promotions = $pdo->query("SELECT * FROM promotions ORDER BY start_date DESC")->fetchAll(PDO::FETCH_ASSOC);
// Получение списка категорий для формы добавления услуг
$categories = $pdo->query("SELECT * FROM service_categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Получение списка услуг с категориями для таблицы
$services = $pdo->query("
    SELECT s.*, GROUP_CONCAT(sc.name ORDER BY sc.name SEPARATOR ', ') as categories
    FROM services s
    LEFT JOIN service_to_category stc ON s.id = stc.service_id
    LEFT JOIN service_categories sc ON stc.category_id = sc.id
    GROUP BY s.id
    ORDER BY s.title
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Smart Build</title>
    <link rel="shortcut icon" href="..\assets\images\logo.ico" type="image/x-icon">
    <style>
       @import url('https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Itim&family=M+PLUS+Rounded+1c&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Russo+One&family=Shafarik&display=swap');

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
/* КНОПКА(ДРУГИЕ СТИЛИ)*/
button.btn.btn-p{
       display: inline-block;
    padding: 5px;
    background-color:#7A1E4C;
    color:white;
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
}
 button.btn.btn-p:hover{color:#7A1E4C;
    background-color:transparent;
    transform: translateY(-2px);
}

/* Контейнер админ-панели */
.admin-container {
    display: flex;
    min-height: 100vh;
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
    case 'portfolio': echo 'Управление портфолио'; break;
    case 'promotions': echo 'Управление акциями'; break;
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
    border: 2px solid transparent;
    color: #7A1E4C;
    
}

.btn-primary:hover {
    background-color: #7A1E4C;
    border: 2px solid #7A1E4C;
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
    background-color: transparent;
    color: #e74c3c;
    border: 1px solid transparent;
}

.btn-danger:hover {
    background-color: #e74c3c;
    border: 1px solid #e74c3c;
    color:white;
    cursor: pointer;
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

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    background-color: #dff0d8;
    border-color: #d6e9c6;
    color: #3c763d;
}

.alert-danger {
    background-color: #f2dede;
    border-color: #ebccd1;
    color: #a94442;
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
    .admin-container {
        flex-direction: column;
    }
    
    .sidebar {
        width: 100%;
    }
    
    .main-content {
        padding: 20px;
    }
}

.roles-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .role-checkbox {
        display: flex;
        align-items: center;
        padding: 5px 10px;
        
        border-radius: 4px;
        cursor: pointer;
    }
    .role-checkbox:hover {
        background: #e9ecef;
    }
    .role-checkbox input {
        margin-right: 5px;
        cursor: pointer;
    }
    select.featured-select {
    border: none;
    cursor: pointer;
    border: 1px solid transparent;
}
select.status-select {
    border: none;
    cursor: pointer;
    border: 1px solid transparent;
}
 select.featured-select:hover,
 select.status-select:hover{
border: 1px solid black;
border-radius:4px;
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
</div>
    <div class="admin-container">
        <!-- Боковая панель -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Админ-панель</h2>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="?tab=dashboard" class="<?= $tab === 'dashboard' ? 'active' : '' ?>">Главная</a></li>
                <li><a href="?tab=users" class="<?= $tab === 'users' ? 'active' : '' ?>">Управление пользователями</a></li>
                <li><a href="?tab=requests" class="<?= $tab === 'requests' ? 'active' : '' ?>">Управление заявками</a></li>
                <li><a href="?tab=services" class="<?= $tab === 'services' ? 'active' : '' ?>">Управление услугами</a></li>
                <li><a href="?tab=promotions" class="<?= $tab === 'promotions' ? 'active' : '' ?>">Управление акциями</a></li>
                <li><a href="?tab=portfolio" class="<?= $tab === 'portfolio' ? 'active' : '' ?>">Управление портфолио</a></li>
                <li><a href="?tab=reviews" class="<?= $tab === 'reviews' ? 'active' : '' ?>">Модерация отзывов</a></li>
            </ul>
            
            <div class="user-info">
                <p><strong><?= htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']) ?></strong></p>
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
                        case 'users': echo 'Управление пользователями'; break;
                        case 'requests': echo 'Управление заявками'; break;
                        case 'services': echo 'Управление услугами'; break;
                        case 'reviews': echo 'Модерация отзывов'; break;
                        default: echo 'Главная';
                    }
                    ?>
                </h1>
            </div>
            
            <!-- Контент для вкладки "Главная" -->
            <div class="tab-content <?= $tab === 'dashboard' ? 'active' : '' ?>">
                <div class="card">
                    <h2>Приветствуем вас, уважаемый администратор!</h2>
                    <p>Вы находитесь в админ-панели Smart Build. Здесь вы можете управлять пользователями, заявками, услугами и отзывами.</p>
                </div>
                
                <div class="card">
                    <h3>Статистика</h3>
                    <?php
                    // Получение статистики
                    $users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                    $requests_count = $pdo->query("SELECT COUNT(*) FROM repair_requests")->fetchColumn();
                    $active_services = $pdo->query("SELECT COUNT(*) FROM services WHERE is_active = 1")->fetchColumn();
                    $pending_reviews = $pdo->query("SELECT COUNT(*) FROM reviews WHERE is_approved = 0")->fetchColumn();
                    ?>
                    
                    <div style="display: flex; gap: 20px; margin-top: 20px;">
                        <div style="flex: 1; background: #e9ecef; padding: 15px; border-radius: 4px;">
                            <h4>Пользователи</h4>
                            <p style="font-size: 24px; font-weight: bold;"><?= $users_count ?></p>
                        </div>
                        <div style="flex: 1; background: #e9ecef; padding: 15px; border-radius: 4px;">
                            <h4>Заявки</h4>
                            <p style="font-size: 24px; font-weight: bold;"><?= $requests_count ?></p>
                        </div>
                        <div style="flex: 1; background: #e9ecef; padding: 15px; border-radius: 4px;">
                            <h4>Активные услуги</h4>
                            <p style="font-size: 24px; font-weight: bold;"><?= $active_services ?></p>
                        </div>
                        <div style="flex: 1; background: #e9ecef; padding: 15px; border-radius: 4px;">
                            <h4>Отзывы на модерации</h4>
                            <p style="font-size: 24px; font-weight: bold;"><?= $pending_reviews ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Контент для вкладки "Пользователи" -->
<div class="tab-content <?= $tab === 'users' ? 'active' : '' ?>">
    <div class="card">
        <h2>Управление пользователями</h2>
        
        <?php
        // Получение списка всех ролей
        $all_roles = $pdo->query("SELECT * FROM roles ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
        
        // Получение списка пользователей с их ролями
        $users = $pdo->query("
            SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.created_at, 
                   GROUP_CONCAT(r.id ORDER BY r.id SEPARATOR ',') as role_ids
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        ?>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роли</th>
                    <th>Дата регистрации</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): 
                    $current_roles = explode(',', $user['role_ids'] ?? '');
                ?>
                    <tr data-user-id="<?= $user['id'] ?>">
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td class="roles-cell">
                            <div class="roles-container">
                                <?php foreach ($all_roles as $role): ?>
                                    <label class="role-checkbox">
                                        <input type="checkbox" 
                                               name="roles" 
                                               value="<?= $role['id'] ?>" 
                                               <?= in_array($role['id'], $current_roles) ? 'checked' : '' ?>
                                               data-role-id="<?= $role['id'] ?>">
                                        <?= htmlspecialchars($role['name']) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <button class="btn btn-danger delete-user" data-user-id="<?= $user['id'] ?>">Удалить</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
            
            <!-- Контент для вкладки "Заявки" -->
            <div class="tab-content <?= $tab === 'requests' ? 'active' : '' ?>">
                <div class="card">
                    <h2>Управление заявками</h2>
                    
                    <?php
                    // Получение списка заявок
                    $requests = $pdo->query("
                        SELECT r.id, u.first_name, u.last_name, u.email, s.title as service, 
                               r.address, r.status, r.created_at, r.updated_at
                        FROM repair_requests r
                        LEFT JOIN users u ON r.user_id = u.id
                        LEFT JOIN services s ON r.service_id = s.id
                        ORDER BY r.created_at DESC
                    ")->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
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
                                    <td><?= htmlspecialchars($request['status']) ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($request['created_at'])) ?></td>
                                    <td>
                                        <a href="view_request1.php?id=<?= $request['id'] ?>" class="btn btn-primary">Просмотр</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
           <!-- Контент для вкладки "Услуги" -->
<div class="tab-content <?= $tab === 'services' ? 'active' : '' ?>">
    <div class="card">
        <h2>Управление услугами</h2>
        
        <!-- Форма добавления новой услуги -->
        <div style="margin-bottom: 20px; padding: 15px; background: #f5f5f5; border-radius: 4px;">
            <h3>Добавить новую услугу</h3>
            <form id="add-service-form" method="post" action="save_service.php">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label>Название услуги:</label>
                        <input type="text" name="title" required style="width: 100%; padding: 8px;">
                    </div>
                    <div>
                        <label>Цена (руб.):</label>
                        <input type="number" name="price" step="0.01" required style="width: 100%; padding: 8px;">
                    </div>
                    <div>
                        <label>Длительность:</label>
                        <input type="text" name="duration" required style="width: 100%; padding: 8px;">
                    </div>
                    <div>
                        <label>Статус:</label>
                        <select name="is_active" style="width: 100%; padding: 8px;">
                            <option value="1">Активна</option>
                            <option value="0">Неактивна</option>
                        </select>
                    </div>
                    <div>
                        <label>Изображение (URL):</label>
                        <input type="text" name="image" style="width: 100%; padding: 8px;">
                    </div>
                    <div>
                        <label>Описание:</label>
                        <textarea name="description" style="width: 100%; padding: 8px; min-height: 80px;"></textarea>
                    </div>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label>Категории:</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php foreach ($categories as $category): ?>
                            <label style="display: flex; align-items: center;">
                                <input type="checkbox" name="categories[]" value="<?= $category['id'] ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-p">Добавить услугу</button>
            </form>
        </div>
        
        <!-- Таблица существующих услуг -->
        <h3>Список услуг</h3>
        <table style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Цена</th>
                    <th>Длительность</th>
                    <th>Категории</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            
                <?php foreach ($services as $service): ?>
                    <tr data-service-id="<?= $service['id'] ?>">
                        <td><?= htmlspecialchars($service['title']) ?></td>
                        <td><?= number_format($service['price'], 2) ?> руб.</td>
                        <td><?= htmlspecialchars($service['duration']) ?></td>
                        <td><?= htmlspecialchars($service['categories']) ?></td>
                        <td>
                            <select class="status-select" data-service-id="<?= $service['id'] ?>" style="padding: 5px;">
                                <option value="1" <?= $service['is_active'] ? 'selected' : '' ?>>Активна</option>
                                <option value="0" <?= !$service['is_active'] ? 'selected' : '' ?>>Неактивна</option>
                            </select>
                        </td>
                        <td>
                    
                            <button class="btn btn-danger delete-service" data-service-id="<?= $service['id'] ?>">Удалить</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
           
        </table>
    </div>
</div>



            <!-- Контент для вкладки "Акции" -->
<div class="tab-content <?= $tab === 'promotions' ? 'active' : '' ?>">
    <div class="card">
        <h2>Управление акциями</h2>
        
        <!-- Форма добавления новой акции -->
        <div style="margin-bottom: 20px; padding: 15px; background: #f5f5f5; border-radius: 4px;">
            <h3>Добавить новую акцию</h3>
            <form id="add-promotion-form" method="post" action="save_promotion.php">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label>Название акции:</label>
                        <input type="text" name="title" required style="width: 100%; padding: 8px;">
                    </div>
                    <div>
                        <label>Размер скидки (%):</label>
                        <input type="number" name="discount_value" step="0.01" required style="width: 100%; padding: 8px;">
                    </div>
                    <div>
                        <label>Дата начала:</label>
                        <input type="date" name="start_date" required style="width: 100%; padding: 8px;">
                    </div>
                    <div>
                        <label>Дата окончания:</label>
                        <input type="date" name="end_date" required style="width: 100%; padding: 8px;">
                    </div>
                    <div>
                        <label>Изображение (URL):</label>
                        <input type="text" name="image" style="width: 100%; padding: 8px;">
                    </div>
                    <div>
                        <label>Статус:</label>
                        <select name="is_active" style="width: 100%; padding: 8px;">
                            <option value="1">Активна</option>
                            <option value="0">Неактивна</option>
                        </select>
                    </div>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label>Описание:</label>
                    <textarea name="description" style="width: 100%; padding: 8px; min-height: 80px;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-p">Добавить акцию</button>
            </form>
        </div>
        
        <!-- Таблица существующих акций -->
        <h3>Список акций</h3>
        <table style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Скидка</th>
                    <th>Период</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promotions as $promotion): ?>
                    <tr data-promotion-id="<?= $promotion['id'] ?>">
                        <td><?= htmlspecialchars($promotion['title']) ?></td>
                        <td><?= number_format($promotion['discount_value'], 2) ?>%</td>
                        <td>
                            <?= date('d.m.Y', strtotime($promotion['start_date'])) ?> - 
                            <?= date('d.m.Y', strtotime($promotion['end_date'])) ?>
                        </td>
                        <td>
                            <select class="status-select" data-promotion-id="<?= $promotion['id'] ?>" style="padding: 5px;">
                                <option value="1" <?= $promotion['is_active'] ? 'selected' : '' ?>>Активна</option>
                                <option value="0" <?= !$promotion['is_active'] ? 'selected' : '' ?>>Неактивна</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-danger delete-promotion" data-promotion-id="<?= $promotion['id'] ?>">Удалить</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


            <!-- Контент для вкладки "Портфолио" -->
<div class="tab-content <?= $tab === 'portfolio' ? 'active' : '' ?>">
    <div class="card">
        <h2>Управление портфолио</h2>
        
            <!-- Форма добавления нового проекта -->
<div style="margin-bottom: 20px; padding: 15px; background: #f5f5f5; border-radius: 4px;">
    <h3>Добавить новый проект</h3>
    <form id="add-project-form" method="post" action="save_project.php">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div>
                <label>Название проекта:</label>
                <input type="text" name="title" required style="width: 100%; padding: 8px;">
            </div>
            <div>
                <label>Услуга:</label>
                <select name="service_id" style="width: 100%; padding: 8px;">
                    <option value="">-- Не выбрано --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Дата завершения:</label>
                <input type="date" name="completed_date" style="width: 100%; padding: 8px;">
            </div>
            <div>
                <label>Рекомендуемый проект:</label>
                <select name="is_featured" style="width: 100%; padding: 8px;">
                    <option value="0">Нет</option>
                    <option value="1">Да</option>
                </select>
            </div>
            <div>
                <label>Изображение "До" (URL):</label>
                <input type="text" name="before_image" style="width: 100%; padding: 8px;">
            </div>
            <div>
                <label>Изображение "После" (URL):</label>
                <input type="text" name="after_image" style="width: 100%; padding: 8px;">
            </div>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Описание проекта:</label>
            <textarea name="description" style="width: 100%; padding: 8px; min-height: 80px;"></textarea>
        </div>
        
        <button type="submit" class="btn btn-p">Добавить проект</button>
    </form>
</div>
        
        <!-- Таблица существующих проектов -->
        <h3>Список проектов</h3>
        <table style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Услуга</th>
                    <th>Дата завершения</th>
                    <th>Рекомендуемый</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr data-project-id="<?= $project['id'] ?>">
                        <td><?= htmlspecialchars($project['title']) ?></td>
                        <td><?= htmlspecialchars($project['service_title'] ?? 'Не указано') ?></td>
                        <td><?= $project['completed_date'] ? date('d.m.Y', strtotime($project['completed_date'])) : 'Не указана' ?></td>
                        <td>
                            <select class="featured-select" data-project-id="<?= $project['id'] ?>" style="padding: 5px;">
                                <option value="0" <?= !$project['is_featured'] ? 'selected' : '' ?>>Нет</option>
                                <option value="1" <?= $project['is_featured'] ? 'selected' : '' ?>>Да</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-danger delete-project" data-project-id="<?= $project['id'] ?>">Удалить</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
            
            <!-- Контент для вкладки "Отзывы" -->
            <div class="tab-content <?= $tab === 'reviews' ? 'active' : '' ?>">
                <div class="card">
                    <h2>Модерация отзывов</h2>
                    
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
                                <th>Действия</th>
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
                                    <td><?= htmlspecialchars(mb_substr($review['comment'], 0, 50)) . (mb_strlen($review['comment']) > 50 ? '...' : '') ?></td>
                                    <td><?= date('d.m.Y', strtotime($review['created_at'])) ?></td>
                                    <td><?= $review['is_approved'] ? 'Одобрен' : 'На модерации' ?></td>
                                    <td>
                                    <a href="approve_review.php?id=<?= $review['id'] ?>" class="btn btn-primary"><?= $review['is_approved'] ? 'Снять' : 'Одобрить' ?></a>
                                    <a href="delete_review.php?id=<?= $review['id'] ?>" class="btn btn-danger" onclick="return confirm('Вы уверены?')">Удалить</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка изменения ролей
    document.querySelectorAll('.roles-cell input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const userId = this.closest('tr').dataset.userId;
            const roleId = this.dataset.roleId;
            const isChecked = this.checked;
            
            // Отправляем AJAX-запрос на сервер
            fetch('update_user_roles.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `user_id=${userId}&role_id=${roleId}&action=${isChecked ? 'add' : 'remove'}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Можно добавить уведомление об успешном обновлении
                    console.log('Роли обновлены');
                } else {
                    console.error('Ошибка:', data.error);
                    // Возвращаем чекбокс в исходное состояние при ошибке
                    this.checked = !isChecked;
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                this.checked = !isChecked;
            });
        });
    });
    
    // Обработка удаления пользователя
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            
            if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
                fetch('delete_user.php?id=' + userId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Удаляем строку из таблицы
                        document.querySelector(`tr[data-user-id="${userId}"]`).remove();
                        // Можно добавить уведомление об успешном удалении
                        alert('Пользователь успешно удален');
                    } else {
                        alert('Ошибка: ' + (data.error || 'Не удалось удалить пользователя'));
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при удалении пользователя');
                });
            }
        });
    });
});



// Добавьте этот код в ваш существующий script-блок
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const requestId = this.dataset.requestId;
        const newStatus = this.value;
        
        fetch('update_request_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${requestId}&status=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Обновляем страницу для отображения изменений
            } else {
                alert('Ошибка при обновлении статуса');
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при обновлении статуса');
        });
    });
});

// Обработка изменения статуса услуги
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const serviceId = this.dataset.serviceId;
        const newStatus = this.value;
        
        fetch('update_service_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${serviceId}&is_active=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Статус услуги успешно обновлен');
            } else {
                alert('Ошибка при обновлении статуса');
                location.reload(); // Перезагружаем страницу, чтобы вернуть предыдущее состояние
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при обновлении статуса');
            location.reload();
        });
    });
});

// Обработка удаления услуги
document.querySelectorAll('.delete-service').forEach(button => {
    button.addEventListener('click', function() {
        const serviceId = this.dataset.serviceId;
        
        if (confirm('Вы уверены, что хотите удалить эту услугу? Это действие нельзя отменить.')) {
            fetch('delete_service.php?id=' + serviceId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Удаляем строку из таблицы
                    document.querySelector(`tr[data-service-id="${serviceId}"]`).remove();
                    alert('Услуга успешно удалена');
                } else {
                    alert('Ошибка: ' + (data.error || 'Не удалось удалить услугу'));
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при удалении услуги');
            });
        }
    });
});


// Обработка изменения статуса акции
document.querySelectorAll('.status-select[data-promotion-id]').forEach(select => {
    select.addEventListener('change', function() {
        const promotionId = this.dataset.promotionId;
        const newStatus = this.value;
        
        fetch('update_promotion_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${promotionId}&is_active=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Статус акции успешно обновлен');
            } else {
                alert('Ошибка при обновлении статуса');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при обновлении статуса');
            location.reload();
        });
    });
});

// Обработка удаления акции
document.querySelectorAll('.delete-promotion').forEach(button => {
    button.addEventListener('click', function() {
        const promotionId = this.dataset.promotionId;
        
        if (confirm('Вы уверены, что хотите удалить эту акцию? Это действие нельзя отменить.')) {
            fetch('delete_promotion.php?id=' + promotionId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`tr[data-promotion-id="${promotionId}"]`).remove();
                    alert('Акция успешно удалена');
                } else {
                    alert('Ошибка: ' + (data.error || 'Не удалось удалить акцию'));
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при удалении акции');
            });
        }
    });
});



// Обработка изменения статуса "Рекомендуемый"
document.querySelectorAll('.featured-select').forEach(select => {
    select.addEventListener('change', function() {
        const projectId = this.dataset.projectId;
        const isFeatured = this.value;
        
        fetch('update_project_featured.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${projectId}&is_featured=${isFeatured}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Статус проекта обновлен');
            } else {
                alert('Ошибка при обновлении статуса');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при обновлении статуса');
            location.reload();
        });
    });
});

// Обработка удаления проекта
document.querySelectorAll('.delete-project').forEach(button => {
    button.addEventListener('click', function() {
        const projectId = this.dataset.projectId;
        
        if (confirm('Вы уверены, что хотите удалить этот проект? Это действие нельзя отменить.')) {
            fetch('delete_project.php?id=' + projectId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`tr[data-project-id="${projectId}"]`).remove();
                    alert('Проект успешно удален');
                } else {
                    alert('Ошибка: ' + (data.error || 'Не удалось удалить проект'));
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при удалении проекта');
            });
        }
    });
});
</script>