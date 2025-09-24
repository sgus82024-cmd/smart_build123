<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);

// Проверка данных
if (empty($data['name']) || empty($data['phone'])) {
  echo json_encode(['error' => 'Заполните имя и телефон']);
  exit;
}

// Запись в БД
$stmt = $pdo->prepare("INSERT INTO repair_requests (user_id, address, description) 
                      VALUES (?, ?, ?)");
$stmt->execute([
  1, // ID временного пользователя
  $data['address'],
  "Заявка от {$data['name']}, тел: {$data['phone']}"
]);

echo json_encode(['success' => 'Заявка принята! Мы скоро свяжемся.']);
?>