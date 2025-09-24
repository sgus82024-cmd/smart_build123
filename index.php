<?php 
// Подключение к базе данных
require_once 'includes/db.php';
$pdo = include 'includes/db.php';

// Подключаем остальные части сайта
include 'includes/header.php'; 
include 'includes/slider.php';
include 'includes/services.php';
include 'includes/about.php';
include 'includes/promotions.php';
include 'includes/reviews.php';
include 'includes/request_form.php';
include 'includes/footer.php';
?>