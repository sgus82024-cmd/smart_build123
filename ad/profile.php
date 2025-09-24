<?php
require_once 'config.php';

// Проверка авторизации
if (!isLoggedIn()) {
    redirect('login.php');
}

// Получение информации о текущем пользователе
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Определение активной вкладки
$tab = $_GET['tab'] ?? 'history';

// Обработка формы обновления профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tab === 'profile') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    if (!empty($first_name) && !empty($last_name) && !empty($phone)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE id = ?");
            $stmt->execute([$first_name, $last_name, $phone, $_SESSION['user_id']]);
            $_SESSION['success_message'] = 'Профиль успешно обновлен';
            redirect('profile.php?tab=profile');
        } catch (PDOException $e) {
            $error = 'Ошибка при обновлении профиля: ' . $e->getMessage();
        }
    } else {
        $error = 'Все поля обязательны для заполнения';
    }
}

// Получение истории заявок пользователя
$requests = $pdo->prepare("
    SELECT r.id, r.status, r.created_at, r.updated_at, 
           s.title as service, s.price, p.title as promotion, p.discount_value
    FROM repair_requests r
    LEFT JOIN services s ON r.service_id = s.id
    LEFT JOIN promotions p ON r.promotion_id = p.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$requests->execute([$_SESSION['user_id']]);
$user_requests = $requests->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: rgba(122, 30, 76, 0.2);
            color: #333;
        }
        
        .container {
             
    display: flex
;
    min-height: 100vh;
    align-items: center;
    justify-content: center;

        }
        
        .sidebar {
               width: 294px;
    background-color: white;
    padding: 20px;
    box-shadow: 0px 0px 32px rgba(0, 0, 0, 0.24);
    border-radius: 15px 0px 0px 15px;
        }
        
        .sidebar-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .menu {
            list-style: none;
        }
        
        .menu-item {
            margin-bottom: 1px;
        }
        
      .menu-link {
    display: block;
    padding: 12px 8px;
    
    text-decoration: none;
    transition: all 0.2s;
    display: flex
;
    align-items: center;
    gap: 5px;
    font-family: 'Inter';
    font-style: normal;
    font-weight: 400;
    font-size: 14px;
    line-height: 20px;
    color: #1F2937;
}
        .menu-link:hover {
             background-color: #97A5B7;
            color: #ffff;
        }
        
        .menu-link.active {
            background-color: #97A5B7;
            color: #ffff;
            font-weight: 500;
        }
        
        .logout-link {
            color: #d32f2f;
            margin-top: 20px;
            display: inline-block;
            padding: 12px 15px;
            text-decoration: none;
        }
        
        .user-info {
                margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #E5E7EB;
        }
        
        .user-name {
font-family: 'Inter';
font-style: normal;
font-weight: 400;
font-size: 16px;
line-height: 24px;
color: #1F2937;
        }
        
        .user-email {
font-family: 'Inter';
font-style: normal;
font-weight: 400;
font-size: 14px;
line-height: 20px;
color: #6B7280;

        }
        
        .main-content {
            flex: 1;
            
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
        }
        
        .close-btn {
              font-size: 45px;
    color: #7A1E4C;
    text-decoration: none;
    text-align: right;
    width: 100%;
    display: flex
;
    align-items: flex-start;
    justify-content: flex-end;
        }
        
        .card {
            
          background-color: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
    padding-top: 0;
    width: 600px;
    border-radius: 0px 12px 12px 12px;
        }
        
        .profile-title {
            font-size: 20px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
       .form-group {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}
        
        .form-label {
    width: 180px;

font-family: 'Inter';
font-style: normal;
font-weight: 400;
font-size: 16px;
line-height: 24px;
color: #1F2937;
}
        
.form-input {
    flex: 1;
    padding: 10px 15px;
    border: none;
    border-radius: 6px;
    background-color: transparent;
    transition: all 0.3s ease;
    text-align: right;
font-family: 'Inter';
font-style: normal;
font-weight: 400;
font-size: 14px;
line-height: 20px;
color: #4B5563;
}
      .form-input:focus {
    outline: none;
    
    background-color: #fff;
    box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}


.form-input[readonly] {
    
    color: #777;
    cursor: not-allowed;
}
        
     .save-btn {
        display: inline-block;
    padding: 5px;
    background-color:#7A1E4C ;
    color:white;
    text-decoration: none;
    border: 2px solid transparent;
    border-radius: 6px;
    font-weight: 1500;
    transition: background-color 0.3s;
    cursor: pointer;
    transition: all 0.3s;
    width: 240px;
    height: 40px;
}

        .profile-info {
    display: flex
;
    align-items: flex-start;
}

        
       .save-btn:hover {
    color:#7A1E4C;
    border:2px solid #7A1E4C;
    transform: translateY(-2px);
    background-color:transparent;
}


        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .table th {
            background-color: #f9f9f9;
            font-weight: 500;
            color: #555;
        }
        
        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
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
        
        .empty-message {
            color: #666;
            font-style: italic;
            margin-top: 20px;
        }
        
        .error-message {
            color: #d32f2f;
            background-color: #fde8e8;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .success-message {
            color: #388e3c;
            background-color: #e8f5e9;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        /* Стили для скрытия/показа вкладок */
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
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-info">
        <!-- Боковая панель -->
        <div class="sidebar">
            
            
            <ul class="menu">
                <li class="menu-item">
                    <a href="?tab=profile" class="menu-link <?= $tab === 'profile' ? 'active' : '' ?>"><svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="25" height="25" fill="url(#pattern0_237_162)"/>
<defs>
<pattern id="pattern0_237_162" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_237_162" transform="scale(0.01)"/>
</pattern>
<image id="image0_237_162" width="100" height="100" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>
Профиль</a>
                </li>
                <li class="menu-item">
                    <a href="?tab=history" class="menu-link <?= $tab === 'history' ? 'active' : '' ?>"><svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="25" height="25" fill="url(#pattern0_237_182)"/>
<defs>
<pattern id="pattern0_237_182" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_237_182" transform="scale(0.01)"/>
</pattern>
<image id="image0_237_182" width="100" height="100" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>
История заявок</a>
                </li>
                <li class="menu-item">
                    <a href="?tab=status" class="menu-link <?= $tab === 'status' ? 'active' : '' ?>"><svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="25" height="25" fill="url(#pattern0_237_165)"/>
<defs>
<pattern id="pattern0_237_165" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_237_165" transform="scale(0.0111111)"/>
</pattern>
<image id="image0_237_165" width="90" height="90" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>
Статус выполнение заявки</a>
                </li>
                <li class="menu-item">
                    <a href="logout.php" class="menu-link"><svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="25" height="25" fill="url(#pattern0_237_168)"/>
<defs>
<pattern id="pattern0_237_168" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_237_168" transform="scale(0.01)"/>
</pattern>
<image id="image0_237_168" width="100" height="100" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAFd0lEQVR4Ae2dv28cRRTHLSTC3xC8O9tQRaIhFQVI/JLlm7l0aRAtiAiQqMztbJDubyLQ0BMiSEJLT4OQaEKA0BB4J3+d2bvbvdm59+Lx+EWyvnu3M9837/u53bN9683Bwem/5bWbV7rGfeobd88b99gb91S/ehk8XtTue9+4Tygr5CaiJ/WNl7vKPVQAPQCDL8i2tg98c3RVBMbqyFAYg+EPvUg74+5/dP36i+xQVqcpPT1NBkKg2treYgdy+p5xtqCFmX1FpzD2QhfcsKvcYVe5O/0jxt5lb6s19o+wCBVmL1KI4cnhcRVm5Y19xN5av4B7yl6gMEPxvMQLKJBpCSiQzPJSIApkWgKZjRZ/AYsXyCzQfZcjnpd4gX0TyGy+eF7iBTILdN/liOclXmDfBDKbL56XeIHMAt13OeJ5iRfYN4HM5ovnJV4gs0D3XY54XuIF9k0gs/ne2C/CzNiXF5rTNnuBAg1DKOztKZC0SAElbfbILAUyEs6OXd645Y4h03crkOmZic5QIKLxTjdXINMzE52hQETjnW6uQOIy+7KaveqN/bYz7hvfuNfiZiWMUiBxoYVXdtKVOm1t34ibOXGUAokLzBv3S5iVGJSwCG3HLe/yjWqN+3A9K4LS1bM3WdNYL8JqXphZZ+zn3rh/w8xaY//0jX2LrdXQnLbZjAs1GoJyu5m/zdKyApkeoygUBTIdCM0Qg6JA0oCIQVEg6UDGoHjj3klyViBJsfUmDZ2+kqAokF62yQ/YoCiQZAYbE4eg3Db23Y3BQ08okKFk0p7fBsUb+1c0FAWSFvzYrL2gKJCxaNP3JUNRIOmh75q5FUrtnvjG2sG5CmQwGpYdk6EoEJbcR00mQVEgo1my7RyC0pq56xVRIL04RB9EQVEgogw2zLdBaWv7T2fcfDX4vIF4Yz/wjfttfR2X7XFbu799bY8O1hvfQCr4BN3eiH6KXV/DZX1MHwefK5DPXjl+afWZtN4eanU3pnMHQgefb+bv+9r+elmPCvSdxSlL8GyYrfXpm/rZ/clWQGr35OzbXxCCZttJAQvbCYN6BAhoAX1n2cIQjI3fawEENMtuLviiomFQnwABveC9Z7f8rTBW3+rfeG/rYgECunWQPpmUwBCM0U8PAQKaVFknbSSQBINcAAK64axPTE4gGYYCmZz1zgnbYNBP4KOnqdAVRwY03Kfb0xIYgjHpgjmAgE5bgo5GAiwwyAwgoCigGp/AEIykvxkBCGj8MnQkJcAKgwwBAqoxxyfADkOBxIe/PlIEhgJZjznusRgMBRIHIBw1BIPtL3Hx3gENi+t2PwFxGFQOIKD9JegjJED/OxsyguqNA5DOc1a6Omb9Ygy9tcZzhhCWWx4sX2iN+z08MvTmM2FC57DdNm62MO7n1rif2sa9LrYEUIeKFVLjuAQAAho3S0eJJQAQULFCahyXAEBA42bpKLEEAAIqVqhAY72RckZQCQa9iNmXhCMDyl6gQEPAUCAZwA1hKJA8gPSuXGdfEk5VUPYChRkiJyh7ezCGshcozBA5QdnbgzGUvUBhhsgJyt4ejKHsBQozRE5Q9vZgDGUvUJghcoKytwdjKHuBwgyRE5S9PRhD2QsUZoicoOztwRjKXqAwQ+QEZW8PxlD2AoUZIicoe3swhrIXKMwQOUHZ24MxlL1AYYbICcrenjf2EcxJTw6PK/YihRhSNmFWlB17a75x98IiXeXudJU7ZC90wQ0JxqKafx1m5Y29y97Wtivy+kVd77ebuu9ZHl3tPmYHsrx280pb2wca9LOg47KY/UBXNLIDIUPfHF3tjLsft5CpCy9vfGvsj5SZCAyYrq5fre2tReW+o+tWFU7/hUSZUDZ0mhI7Mv6H8R8jdzzxz8J+yQAAAABJRU5ErkJggg=="/>
</defs>
</svg>
Выйти</a>
                </li>
            </ul>
            
            
        </div>
        
        <!-- Основное содержимое -->
        <div class="main-content">
      
                
            
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message"><?= $_SESSION['success_message'] ?></div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>
            
            <!-- Вкладка истории заявок -->
            <div class="tab-content <?= $tab === 'history' ? 'active' : '' ?>">
                <div class="card">
                    <a href="../index.php" class="close-btn">×</a>
                    <h2 class="profile-title">История ваших заявок</h2>
                    
                    <?php if (empty($user_requests)): ?>
                        <p class="empty-message">У вас пока нет заявок</p>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Что вы заказали</th>
                                    <th>Цена</th>
                                    <th>Дата подачи заявки</th>
                                    <th>Акция</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user_requests as $request): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($request['service']) ?></td>
                                        <td>
                                            <?= number_format($request['price'], 2) ?> руб.
                                            <?php if ($request['discount_value'] > 0): ?>
                                                <br><small>Скидка: <?= $request['discount_value'] ?>%</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d.m.Y', strtotime($request['created_at'])) ?></td>
                                        <td><?= $request['promotion'] ?? '—' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Вкладка статуса заявок -->
            <div class="tab-content <?= $tab === 'status' ? 'active' : '' ?>">
                <div class="card">
                    <a href="../index.php" class="close-btn">×</a>
                    <h2 class="profile-title">Статус ваших заявок</h2>
                    
                    <?php if (empty($user_requests)): ?>
                        <p class="empty-message">У вас пока нет активных заявок</p>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Что вы заказали</th>
                                    <th>Дата подачи заявки</th>
                                    <th>Статус</th>
                                    <th>Последнее обновление</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user_requests as $request): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($request['service']) ?></td>
                                        <td><?= date('d.m.Y', strtotime($request['created_at'])) ?></td>
                                        <td>
                                            <span class="status status-<?= $request['status'] ?>">
                                                <?php 
                                                switch($request['status']) {
                                                    case 'new': echo 'В обработке'; break;
                                                    case 'in_progress': echo 'Передали специалистам'; break;
                                                    case 'completed': echo 'Заявка закрыта'; break;
                                                    case 'canceled': echo 'Отменена'; break;
                                                    default: echo $request['status'];
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td><?= date('d.m.Y H:i', strtotime($request['updated_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Вкладка профиля -->
            <div class="tab-content <?= $tab === 'profile' ? 'active' : '' ?>">
                
            <div class="card">
                <a href="../index.php" class="close-btn">×</a>
                <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
            </div>
                    
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="first_name" class="form-label">Имя</label>
                            <input type="text" id="first_name" name="first_name" class="form-input" required 
                                    placeholder="<?= htmlspecialchars($user['first_name']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name" class="form-label">Фамилия</label>
                            <input type="text" id="last_name" name="last_name" class="form-input" required 
                                    placeholder="<?= htmlspecialchars($user['last_name']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email аккаунт</label>
                            <input type="email" id="email" name="email" class="form-input" readonly 
                                   value="<?= htmlspecialchars($user['email']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">Номер телефона</label>
                            <input type="tel" id="phone" name="phone" class="form-input" required 
                                    placeholder="<?= htmlspecialchars($user['phone']) ?>">
                        </div>
                        
                        <button type="submit" class="save-btn">Сохранить изменения</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>
</html>