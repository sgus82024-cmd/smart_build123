<?php
require_once 'config.php';

// Уничтожение сессии
$_SESSION = [];
session_destroy();

// Перенаправление на страницу входа
redirect('login.php');
?>