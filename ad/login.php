<?php
require_once 'config.php';


if (isLoggedIn()) {
    if (hasRole('admin', $_SESSION['user_id'])) {
        redirect('admin.php');
    } elseif (hasRole('manager', $_SESSION['user_id'])) {
        redirect('manager.php');
    } else {
        redirect('profile.php');
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email)) $errors[] = "Email обязателен";
    if (empty($password)) $errors[] = "Пароль обязателен";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            
            // Проверяем роли и перенаправляем
            if (hasRole('admin', $user['id'])) {
                redirect('admin.php');
            } elseif (hasRole('manager', $user['id'])) {
                redirect('manager.php');
            } else {
                redirect('profile.php');
            }
        } else {
            $errors[] = "Неверный email или пароль";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Smart Build</title>
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
        body {
     font-family: "Roboto", sans-serif;
    background: rgba(122, 30, 76, 0.31);
    margin: 0;
    padding: 0;
}
.wrapper {
    display: flex
;
    height: 100vh;
    justify-content: center;
    margin: 0 auto;
    align-items: center;
}
.container {
     display: flex
;
    width: 69%;
    margin: 0 auto;
    padding: 20px;
    background-image: url(https://media.licdn.com/dms/image/v2/C511BAQHwyhgChF32-w/company-background_10000/company-background_10000/0/1583854866040/amplify_social_media_agency_cover?e=2147483647&v=beta&t=BeoB3BUheC_ljJf9UwIHh7-_1fmxBNae1SnMnKJY7tc);
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    flex-direction: column;
    height: 76%;
    align-items: center;
    justify-content: center;
    background-size: cover;
}

h2 {
font-family: 'Roboto';
font-style: normal;
font-weight: 300;
font-size: 28px;
line-height: 41px;
color: #F9FAFB;
}

/* Стили для формы */
.form-group {
    margin-bottom: 20px;
}

/* Стили для label */
.form-group label {
    height: 25px;
    font-family: 'Roboto', sans-serif;
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 19px;
    color: #FFFFFF;
    display: block;
    margin-bottom: 8px;
}

/* Стили для полей ввода */
.form-group input[type="email"],
.form-group input[type="password"] {
width: 260px;
height: 55px;
background-color: #222529 !important;
border: 1px solid #888B93;
border-radius: 4px;
color:white;
}
:-webkit-autofill,
    :-webkit-autofill:hover, 
    :-webkit-autofill:focus {
        -webkit-text-fill-color: white !important;
        -webkit-box-shadow: 0 0 0px 1000px #222529 inset !important;
        transition: background-color 5000s ease-in-out 0s;
    }
/* Стиль для фокуса */
.form-group input[type="email"]:focus,
.form-group input[type="password"]:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
}

/* Стиль для кнопки */
button[type="submit"] {
      background: #7A1E4C;
    border: 2px solid #7A1E4C;
    border-radius: 4px;
    color: white;
    margin: 0 auto;
    padding: 15px 40px;
    font-size: 12px;
    text-transform: uppercase;
    margin-top: 30px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex
;
    font-family: "Inter", sans-serif;
    font-weight: 700;
    text-align: center;
    transform: scaleX(180deg);
}

button[type="submit"]:hover {
    color: white;
    background: transparent;
    transform: translateY(-2px);
    border: 2px solid #7A1E4C;
}

/* Стиль для ссылки "Забыли пароль" */
.forgot-password {
    text-align: right;
    margin-bottom: 20px;
}
 p{
    color:white;
}
.forgot-password a {
    color: #7A1E4C;
    cursor: pointer;
    font-size: 14px;
    transition: color 0.3s ease;
}

.forgot-password a:hover {
    color: #FFFFFF;
}


.cont-login {
        width: 30%;
    margin: 0 auto;
    display: flex
;
    flex-direction: column;
    align-items: center
}


.register-link {
    text-align: center;
    margin-top: 15px;
}
.register-link a{
    color: #7A1E4C;
}
&::placeholder {
        color: #888B93;
        opacity: 1;
    }
.close-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1000;
}

.close-btn {
  display: block;
  width: 40px;
  height: 40px;
  background: rgba(255,255,255,0.7);
  border-radius: 50%;
  position: relative;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
  transition: all 0.3s ease;
}

.close-btn:hover {
  background: rgba(255,0,0,0.7);
  transform: scale(1.1);
}

.close-icon {
  position: absolute;
  width: 100%;
  height: 100%;
}
span.close-icon {
    color: #000000;
    font-size: 20px;
}
.close-icon {
    position: absolute;
    width: 100%;
    height: 100%;
    display: flex
;
    align-items: center;
    justify-content: center;
}
.close-icon:before,
.close-icon:after {
  background-color: #000;
}
.error {
        background: #974153;
    padding: 5px;
    display: flex
;
    flex-direction: column;
    margin-bottom: 15px;
    background: #222529;
 
    position: absolute;
    right: 300px;
    border-radius: 15px;
    transition:  5000s ease-in-out 
}
.error p {
        color: #ff0000;
    margin-bottom: 15px;
    text-align: center;
}
    </style>
</head>
<body>
    <div class="wrapper">
    <div class="container">
   <div class="close-container">
  <a href="../index.php" class="close-btn">
    <span class="close-icon">&times;</span>
  </a>
</div>
        <div class="cont-login">
        <h2>ВХОД</h2>
        
        
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Введите E-mail" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль (не менее 8 символов)</label>
                <input type="password" id="password" name="password" placeholder="Введите пароль" required minlength="8">
            </div>
            
            <div class="forgot-password">
                <a href="forgot_password.php">Забыли пароль?</a>
            </div>
            <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
            <button type="submit">ВХОД</button>
        </form>
        
        <div class="register-link">
            <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
        </div>
    </div>
    </div>
</div>
</body>
</html>