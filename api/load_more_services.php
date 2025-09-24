<?php
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: text/html');

if (!isset($pdo)) {
    die("Ошибка: Подключение к базе данных не установлено");
}

// Получаем параметры из AJAX запроса
$offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 6;

try {
    // Запрос к базе данных
    $stmt = $pdo->prepare("SELECT * FROM services LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    // Рендерим новые карточки
    $output = '';
    while ($service = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $output .= renderServiceItem($service);
    }
    
    echo $output;
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo '';
}

// Функция для рендеринга карточки услуги
function renderServiceItem($service) {
    $html = '<div class="service__item service__item--3x2">';
    if (!empty($service['image'])) {
        $html .= '<div class="service__image service__image--3x2">';
        $html .= '<img src="../assets/images/' . htmlspecialchars($service['image']) . '" alt="' . htmlspecialchars($service['title']) . '">';
        $html .= '</div>';
    }
    $html .= '<h3 class="service__title--3x2">' . htmlspecialchars($service['title']) . '</h3>';
    $html .= '<p class="price price--3x2">' . number_format($service['price'], 0, '', ' ') . ' ₽</p>';
    $html .= '<p class="old-price old-price--3x2">' . number_format($service['price'] * 1.4, 0, '', ' ') . ' ₽</p>';
    $html .= '<a href="#" class="btn btn--3x2 request-btn">Заказать</a>';
    $html .= '</div>';
    
    return $html;
}