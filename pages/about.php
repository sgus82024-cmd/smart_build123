<?php
require_once __DIR__ . '/../includes/db.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О компании Smart Build - профессиональный ремонт квартир и коттеджей</title>
    <link rel="shortcut icon" href="..\assets\images\logo.ico" type="image/x-icon">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        
        .content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            margin-top: 83px;
        }
        .request-form{
                background-color: transparent !important; 
        }
        footer h3 {
    border-bottom: none !important;
}
        .title-company {
    display: flex
;
    justify-content: center;
    font-size: 20px;
}
        h2, h3, h4 {
            color: #000;
        }
        .advantage-card h4 {
    color:#7a1e4c;
  
}
        h2 {
            font-size: 2.5em;
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        h2:after {
            content: "";
            display: block;
            width: 100px;
            height: 4px;
            background: #7a1e4c;
            margin: 15px auto;
        }
        
        h3 {
            font-size: 1.8em;
            margin-top: 40px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .intro-box {
               background: #7a1e4c21;
    padding: 25px;
    border-left: 4px solid #7a1e4c;
    margin: 30px 0;
    font-size: 1.1em;
    border-radius: 0 8px 8px 0;

        }
        
        .mission-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 30px 0;
        }
        
   .mission-item {
            flex-basis: 30%;
            min-width: 250px;
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.5s ease;
            opacity: 0.9;
            transform: translateY(0);
        }
        
        .mission-item.active {
            transform: translateY(-10px) scale(1.05);
            opacity: 1;
            background-color: rgba(122, 30, 76, 0.05);
            box-shadow: 0 6px 12px rgba(122, 30, 76, 0.15);
            border-left: 3px solid #7a1e4c;
        }
        
        .mission-item.active .mission-icon img {
            transform: scale(1.2);
            transition: transform 0.5s ease;
        }
        
 
        .mission-item h3 {
            margin-top: 0;
        }
        
        .mission-icon {
            font-size: 2em;
            color: #2a7ae9;
            margin-bottom: 15px;
        }
        
        .quality-banner {
            text-align: center;
            margin: 40px 0;
            padding: 20px;
            background: #7a1e4c21;;
            border-radius: 8px;
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .quality-banner span {
            font-size: 1.5em;
            color: #7a1e4c;
            vertical-align: middle;
            margin: 0 10px;
        }
        
        .advantages-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .advantage-card {
            background: white;
            padding: 45px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .advantage-card h3 {
            margin-top: 0;
            display: flex;
            align-items: center;
        }

        .form-container {
    width: 1200px !important;
}
        
        .advantage-card h3:before {
            content: "✓";
            color: #2a7ae9;
            font-size: 1.5em;
            margin-right: 10px;
        }
        .modal-content h2:after {
    display:none;
}
        .values-box {
           
    background: #7a1e4c21;
    padding: 45px;
    border-radius: 8px;
    margin: 30px 0;

        }
        
        .work-steps {
            counter-reset: step;
            margin: 30px 0;
        }
        
        
        
        .work-step {
            position: relative;
            padding-left: 60px;
            margin-bottom: 25px;
            transition: all 0.5s ease;
            opacity: 0.8;
        }
        
        .work-step.active {
            transform: scale(1.05);
            opacity: 1;
            background-color: rgba(122, 30, 76, 0.05);
            padding: 15px 20px 15px 60px;
            
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(122, 30, 76, 0.1);
        }
        
        .work-step:before {
            counter-increment: step;
            content: counter(step);
            position: absolute;
            left: 0;
            top: 0;
            width: 40px;
            height: 40px;
            background: #7a1e4c;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2em;
            transition: all 0.5s ease;
        }
        
        .work-step.active:before {
            transform: scale(1.2);
            background: #7a1e4c;
            box-shadow: 0 0 0 3px rgba(122, 30, 76, 0.3);
            
        }
        
      
        .highlight-box {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin: 40px 0;
            text-align: center;
        }
        
        .final-motto {
            text-align: center;
            font-size: 1.5em;
            margin: 50px 0;
            color: #2a7ae9;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .mission-item {
                flex-basis: 100%;
            }
            
            .advantages-container {
                grid-template-columns: 1fr;
            }
        }

        /* АДПАТИВ */

           @media (min-width: 992px) and (max-width: 1199px) {
.form-container {
    width: 100% !important; 
}
.request-form {
    padding: 60px 23px;
}
   }
   @media (min-width: 768px) and (max-width: 991px) {
.form-container {
    width: 100% !important; 
}
.request-form {
    padding: 60px 23px;
}
.work-example {
    width: 480px;
}
.about-info p {
    font-size: 13px;
}
.review__text {
    height: 175px;
}

.footer__menu {
    margin-left: 10px;
}
}
@media (min-width: 480px) and (max-width: 767px) {
.form-container {
    width: 100% !important; 
}
.request-form {
    padding: 60px 23px;
}
.work-example {
    width: 480px;
}
.about-info p {
    font-size: 13px;
}
.review__text {
    height: 175px;
}

.footer__menu {
    margin-left: 10px;
}
.about-title {
    font-size: 30px;
}
.reviews__grid {
    grid-template-columns: repeat(1, 1fr);
    gap: 38px;
}
.promotions__grid {
    grid-template-columns: repeat(1, 1fr);
    align-items: center;
    justify-items: center;
}
.promo__item h3 {
    font-size: 18px;
}
.promo__item {
    width: 80%;
}
.price {
    font-size: 17px;
}
.subtitle {
    font-size: 20px;
    text-align: center;
}
.form-container h1 {
    font-size: 11px;
}
.submit-btn {
    margin:0;
}
.footer__menu a {
    justify-content: center;
    text-align: center;
}
.footer__menu {
        margin-left: 10px;
        align-items: center;
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-content: center;
    }
    .footer__developers {
    display: flex;
    flex-direction: column;
    align-items: center;
}
    .about-title {
        font-size: 19px;
    }
    .service__item {
    width: 80%;
}
.description {
    color: white;
    font-size: 23px;
    margin-bottom: 20px;
    font-family: "Roboto", sans-serif;
    font-weight: 200;
}
.footer__content {
    display: grid
;
    grid-template-columns: 2fr;
    gap: 60px;
    margin-bottom: 40px;
    justify-items: center;
}
.info-slide h4 {
    width: 250px;
}
}


@media (min-width: 320px) and (max-width: 479px) 
{    .work-example {
        width: 300px;
    }
   .about-content {
    display: flex
;
    gap: 30px;
    align-items: center;
    flex-direction: column;
} 
.footer__content {
    display: grid
;
    grid-template-columns: 2fr;
    gap: 60px;
    margin-bottom: 40px;
    justify-items: center;
}
.form-container {
    width: 100% !important; 
}
.request-form {
    padding: 60px 23px;
}
.work-example {
    width: 300px;
}
.about-info p {
    font-size: 13px;
}
.review__text {
    height: 175px;
}

.footer__menu {
    margin-left: 0;
}
    .about-title {
        font-size: 23px;
        margin: 20px auto;
        text-align: center;
    }
.reviews__grid {
    grid-template-columns: repeat(1, 1fr);
    gap: 38px;
}
.promotions__grid {
    grid-template-columns: repeat(1, 1fr);
    align-items: center;
    justify-items: center;
}
.promo__item h3 {
    font-size: 18px;
}
.promo__item {
    width: 80%;
}
.price {
    font-size: 17px;
}
.subtitle {
    font-size: 20px;
    text-align: center;
}
.form-container h1 {
    font-size: 11px;
}
    .submit-btn {
        margin-bottom: 10px;
    }
.footer__menu a {
    justify-content: center;
    text-align: center;
    
}
.footer__menu {
        margin-left: 0px;
        align-items: center;
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-content: center;
        padding: 19px;
    }
    .footer__developers {
    display: flex;
    flex-direction: column;
    align-items: center;
}
    .about-title {
        font-size: 19px;
    }
    .service__item {
    width: 80%;
}
.description {
    color: white;
    font-size: 23px;
    margin-bottom: 20px;
    font-family: "Roboto", sans-serif;
    font-weight: 200;
}
.footer__content {
    display: grid
;
    grid-template-columns: 2fr;
    gap: 60px;
    margin-bottom: 40px;
    justify-items: center;
}
.info-slide h4 {
    width: 250px;
}
.footer__developers a {
    font-size: 15px; 
}
.btn {
    width: 100%;}
    .form-container {
        padding: 11px;
        height: 100%;
    }
    .form-input, .form-textarea {
    width: 200px !important;
}
.form-container {
        width: 100% !important;
    }
.form-select {
    width: 200px;
}
.footer__left-column {
    grid-template-columns: 1fr;
}

}
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header.php'; ?>
    
    <div class="content-wrapper">
        <div class="title-company">
        <h3>О компании Smart Build</h3></div>
        
        <div class="intro-box">
            <strong>Smart Build</strong> – это команда профессионалов, которая превращает ваше жилье в идеальное пространство. Мы объединяем инновационные технологии, качественные материалы и индивидуальный подход, чтобы каждый проект был выполнен безупречно.
        </div>
        
        <h3>Наша миссия</h3>
        
        <div class="mission-container">
            <div class="mission-item">
                <div class="mission-icon"><img width="40" height="40" src="https://img.icons8.com/ios-filled/50/7a1e4c/home.png" alt="home"/></div>
                <h4>Комфорт</h4>
                <p>Создавать уютные и функциональные дома, где каждая деталь продумана для вашего удобства и комфорта.</p>
            </div>
            
            <div class="mission-item">
                <div class="mission-icon"><img width="40" height="40" src="https://img.icons8.com/ios-filled/50/7a1e4c/good-quality--v1.png" alt="good-quality--v1"/></div>
                <h4>Качество</h4>
                <p>Гарантировать высочайшее качество на всех этапах – от разработки дизайна до финальной отделки.</p>
            </div>
            
            <div class="mission-item">
                <div class="mission-icon"><img width="40" height="40" src="https://img.icons8.com/glyph-neue/40/7a1e4c/trust--v1.png" alt="trust--v1"/></div>
                <h4>Доверие</h4>
                <p>Строить долгосрочные отношения с клиентами, основанные на прозрачности и взаимном уважении.</p>
            </div>
        </div>
        
        <div class="quality-banner">
            <span>★</span> Качество – наш главный приоритет <span>★</span>
        </div>
        
        <h3>Почему выбирают нас?</h3>
        
        <div class="advantages-container">
            <div class="advantage-card">
                <h4>Опыт и надежность</h4>
                <ul>
                    <li>Более <strong>10 лет</strong> на рынке ремонтных услуг</li>
                    <li>Реализовали <strong>500+</strong> успешных проектов</li>
                    <li>Работаем с частными и коммерческими объектами</li>
                    <li>Собственная бригада проверенных мастеров</li>
                </ul>
            </div>
            
            <div class="advantage-card">
                <h4>Индивидуальный подход</h4>
                <ul>
                    <li>Бесплатный выезд замерщика и консультация</li>
                    <li>Гибкие условия сотрудничества</li>
                    <li>Учет всех пожеланий клиента</li>
                    <li>Возможность разработки индивидуального дизайн-проекта</li>
                </ul>
            </div>
            
            <div class="advantage-card">
                <h4>Качество без компромиссов</h4>
                <ul>
                    <li>Используем только <strong>проверенные материалы</strong></li>
                    <li>Соблюдаем сроки и бюджет</li>
                    <li>Предоставляем <strong>гарантию</strong> на все работы</li>
                    <li>Поэтапный контроль качества</li>
                </ul>
            </div>
        </div>
        
        <div class="values-box">
            <h4>Наши ценности:</h4>
            <ul>
                <li><strong>Профессионализм</strong> – работаем только с лучшими мастерами, которые регулярно повышают квалификацию</li>
                <li><strong>Ответственность</strong> – строго соблюдаем договоренности и взятые обязательства</li>
                <li><strong>Инновации</strong> – применяем современные технологии и материалы в ремонте</li>
                <li><strong>Честность</strong> – прозрачное ценообразование без скрытых платежей</li>
            </ul>
        </div>
        <h3>Как мы работаем?</h3>
        
    <div class="work-steps">
        <div class="work-step">
            <h4>Консультация</h4>
            <p>Обсуждение ваших идей, предпочтений и бюджета. На этом этапе мы знакомимся с вами и вашим проектом.</p>
        </div>
        
        <div class="work-step">
            <h4>Замеры и расчет</h4>
            <p>Точные замеры помещения, разработка детального плана работ и прозрачной сметы.</p>
        </div>
        
        <div class="work-step">
            <h4>Дизайн-проект</h4>
            <p>Разработка визуализации будущего ремонта (по вашему желанию) с учетом всех нюансов.</p>
        </div>
        
        <div class="work-step">
            <h4>Ремонт</h4>
            <p>Поэтапное выполнение работ с регулярным контролем качества и фотоотчетами для вас.</p>
        </div>
        
        <div class="work-step">
            <h4>Сдача проекта</h4>
            <p>Финальная проверка всех работ, устранение недочетов (если есть) и ваше одобрение результата.</p>
        </div>
    </div>
   
        
        <?php include_once __DIR__ . '/../includes/request_form.php'; ?>
        
        
    </div>
    
    <?php include_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>

   
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const steps = document.querySelectorAll('.work-step');
        let currentStep = 0;
        const totalSteps = steps.length;
        const stepDuration = 2000; // 2 секунды на каждый шаг (итого 10 секунд на цикл)
        
        function activateStep(index) {
            // Удаляем класс active у всех шагов
            steps.forEach(step => step.classList.remove('active'));
            
            // Добавляем класс active текущему шагу
            steps[index].classList.add('active');
            
            // Переходим к следующему шагу
            currentStep = (index + 1) % totalSteps;
            
            // Запускаем следующий шаг через заданный интервал
            setTimeout(() => activateStep(currentStep), stepDuration);
        }
        
        // Начинаем анимацию
        setTimeout(() => activateStep(0), 500); // Небольшая задержка перед началом
    });
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Находим все элементы миссии
    const missionItems = document.querySelectorAll('.mission-item');
    let currentMission = 0;
    const totalMissions = missionItems.length;
    const missionDuration = 2000; // 2 секунды на каждый элемент
    
    // Функция для активации текущего элемента миссии
    function activateMission(index) {
        // Удаляем класс active у всех элементов
        missionItems.forEach(item => {
            item.classList.remove('active');
        });
        
        // Добавляем класс active текущему элементу
        missionItems[index].classList.add('active');
        
        // Переходим к следующему элементу
        currentMission = (index + 1) % totalMissions;
        
        // Запускаем следующий элемент через заданный интервал
        setTimeout(() => activateMission(currentMission), missionDuration);
    }
    
    // Начинаем анимацию через небольшую задержку после загрузки страницы
    setTimeout(() => {
        activateMission(0);
    }, 500);
});
</script>