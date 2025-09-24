<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (hasRole('admin')) {
    redirect('admin.php');
} elseif (hasRole('manager')) {
    redirect('manager.php');
} else {
    redirect('profile.php');
}