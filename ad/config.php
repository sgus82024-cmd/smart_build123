<?php
// Конфигурация базы данных
define('DB_HOST', '127.0.0.1:3306');
define('DB_NAME', 'smart_build');
define('DB_USER', 'root');
define('DB_PASS', '');

// Настройки сессии
session_start();

// Подключение к базе данных
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Функция для редиректа
function redirect($url) {
    header("Location: $url");
    exit();
}

// Проверка авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}
// Проверка, что пользователь является менеджером
    function isManager() {
        return hasRole('manager');
    }

// Проверка роли пользователя
function hasRole($role_name, $user_id = null) {
    global $pdo;
    
    // Если user_id не передан, используем текущего пользователя
    $user_id = $user_id ?? ($_SESSION['user_id'] ?? null);
    if (!$user_id) return false;
    
    $stmt = $pdo->prepare("SELECT r.name FROM roles r 
                          JOIN user_roles ur ON r.id = ur.role_id 
                          WHERE ur.user_id = ?");
    $stmt->execute([$user_id]);
    $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    return in_array($role_name, $roles);
}
function redirectBasedOnRole($user_id = null) {
    if (hasRole('admin', $user_id)) {
        redirect('admin.php');
    } elseif (hasRole('manager', $user_id)) {
        redirect('manager.php');
    } else {
        redirect('profile.php');
    }
}

function getCurrentUserRole() {
    if (!isLoggedIn()) return null;
    
    if (hasRole('admin')) return 'admin';
    if (hasRole('manager')) return 'manager';
    return 'user';
}