<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($pdo)) {
    die("Ошибка: Подключение к базе данных не установлено");
}

// Получаем количество всех проектов с after_image
$totalStmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE after_image IS NOT NULL");
$totalProjects = $totalStmt->fetchColumn();

// Получаем первые 6 проектов
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$stmt = $pdo->prepare("SELECT * FROM projects WHERE after_image IS NOT NULL ORDER BY is_featured DESC, completed_date DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Передаем общее количество проектов в JavaScript
echo '<script>const totalProjects = ' . $totalProjects . ';</script>';

include_once __DIR__ . '/../includes/header.php'; 
?>

<style>
    /* Основные стили портфолио */
    .portfolio-smartbuild {
        font-family: 'Roboto', sans-serif;
        background-color: #ffffff;
        margin: 0;
        padding: 80px 0;
    }
    
    .portfolio-smartbuild__container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }
    
    .portfolio-smartbuild__title {
        font-size: 32px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 40px;
        color: #222;
        text-transform: uppercase;
        letter-spacing: 1.4px;
    }
    
    .portfolio-smartbuild__grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-bottom: 40px;
    }
    
    @media (max-width: 992px) {
        .portfolio-smartbuild__grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .portfolio-smartbuild__grid {
            grid-template-columns: 1fr;
        }
    }
    
    .portfolio-smartbuild__item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        aspect-ratio: 1/1;
        cursor: pointer;
    }
    
    .portfolio-smartbuild__item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .portfolio-smartbuild__image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }
    
    .portfolio-smartbuild__item:hover .portfolio-smartbuild__image {
        transform: scale(1.05);
    }
    
    .portfolio-smartbuild__overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(122, 30, 76, 0.8), transparent);
        padding: 20px;
        color: white;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }
    
    .portfolio-smartbuild__item:hover .portfolio-smartbuild__overlay {
        transform: translateY(0);
    }
    
    .portfolio-smartbuild__project-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .portfolio-smartbuild__project-date {
        font-size: 14px;
        opacity: 0.9;
    }
    
    .portfolio-smartbuild__load-more {
        text-align: center;
        margin-top: 40px;
    }
    
    .portfolio-smartbuild__load-btn {
        background: #7A1E4C;
        border: 1px solid #7A1E4C;
        border-radius: 4px;
        color: white;
        padding: 15px 40px;
        font-size: 12px;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.3s;
        font-family: "Inter", sans-serif;
        font-weight: 700;
    }
    
    .portfolio-smartbuild__load-btn:hover {
        background-color: transparent;
        color: #7A1E4C;
        border-color: #7A1E4C;
    }

    /* Стили для шапки модального окна */
    .portfolio-modal__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 30px;
        border-bottom: 1px solid #eee;
        background-color: #f9f9f9;
        position: relative;
    }
    
    .portfolio-modal__logo {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
    }
    
    .portfolio-modal__header-text {
        font-size: 18px;
        font-weight: 600;
        color: black;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }
    
    button#closeModal {
    color: #7A1E4C;
    }

    .portfolio-modal__close {
        color: #7A1E4C;
        font-size: 28px;
        font-weight: 300;
        cursor: pointer;
        background: none;
        border: none;
        padding: 5px;
        transition: transform 0.2s;
        flex-shrink: 0;
    }
    
    .portfolio-modal__close:hover {
        transform: translateY(2px) ;
    }
    
    /* Стили модального окна */
    .portfolio-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        z-index: 2000;
        overflow-y: auto;
        padding: 20px;
        box-sizing: border-box;
    }
    
    .portfolio-modal__content {
        background-color: white;
        max-width: 1000px;
        margin: 40px auto;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
    }
    
    .portfolio-modal__close {
        position: absolute;
        top: 20px;
        right: 20px;
        color: white;
        font-size: 30px;
        cursor: pointer;
        z-index: 10;
    }
    
    .portfolio-modal__body {
        display: flex;
        flex-direction: row;
    }
    
    @media (max-width: 768px) {
        .portfolio-modal__body {
            flex-direction: column;
        }
    }
    
    .portfolio-modal__slider {
        flex: 1;
        min-width: 0;
        padding: 20px;
        background-color: #f9f9f9;
    }
    
    .portfolio-modal__info {
        flex: 1;
        min-width: 0;
        padding: 30px;
    }
    
    .portfolio-modal__title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #7A1E4C;
    }
    
    .portfolio-modal__description {
        margin-bottom: 20px;
        line-height: 1.6;
    }
    
    .portfolio-modal__details {
        margin-bottom: 20px;
    }
    
    .portfolio-modal__detail-item {
        display: flex;
        margin-bottom: 10px;
    }
    
    .portfolio-modal__detail-label {
        font-weight: 700;
        min-width: 120px;
        color: #555;
    }
    
    .portfolio-modal__detail-value {
        flex: 1;
    }
    
    /* Стили слайдера */
    .portfolio-slider {
        position: relative;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    
    .portfolio-slider__container {
        position: relative;
        width: 100%;
        height: 400px;
    }
    
    .portfolio-slider__slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    
    .portfolio-slider__slide.active {
        opacity: 1;
    }
    
    .portfolio-slider__image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    
    .portfolio-slider__caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 10px;
        font-size: 14px;
        text-align: center;
    }
    
    .portfolio-slider__controls {
        display: flex;
        justify-content: space-between;
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        z-index: 10;
    }
    
    .portfolio-slider__btn {
        background-color: rgba(122, 30, 76, 0.7);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        transition: background-color 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 10px;
    }
    
    .portfolio-slider__btn:hover {
        background-color: rgba(122, 30, 76, 0.9);
    }
    
    .portfolio-slider__dots {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }
    
    .portfolio-slider__dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #ccc;
        margin: 0 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .portfolio-slider__dot.active {
        background-color: #7A1E4C;
    }
</style>

<div class="portfolio-smartbuild">
    <div class="portfolio-smartbuild__container">
        <h1 class="portfolio-smartbuild__title">Наше портфолио</h1>
        
        <div class="portfolio-smartbuild__grid">
            <?php foreach ($projects as $project): ?>
                <div class="portfolio-smartbuild__item" 
                     data-project-id="<?php echo $project['id']; ?>"
                     data-title="<?php echo htmlspecialchars($project['title']); ?>"
                     data-description="<?php echo htmlspecialchars($project['description']); ?>"
                     data-before-image="<?php echo $project['before_image'] ? '../assets/images/portfolio/'.$project['before_image'] : ''; ?>"
                     data-after-image="<?php echo '../assets/images/portfolio/'.$project['after_image']; ?>"
                     data-completed-date="<?php echo date('d.m.Y', strtotime($project['completed_date'])); ?>"
                     data-service-id="<?php echo $project['service_id']; ?>">
                    <img src="../assets/images/portfolio/<?php echo htmlspecialchars($project['after_image']); ?>" 
                         alt="<?php echo htmlspecialchars($project['title']); ?>" 
                         class="portfolio-smartbuild__image">
                    <div class="portfolio-smartbuild__overlay">
                        <h3 class="portfolio-smartbuild__project-title"><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="portfolio-smartbuild__project-date">
                            Завершено: <?php echo date('d.m.Y', strtotime($project['completed_date'])); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="portfolio-smartbuild__load-more">
            <button class="portfolio-smartbuild__load-btn" id="loadMoreProjects">
                Загрузить ещё
            </button>
        </div>
    </div>
</div>

<!-- Модальное окно для проектов -->
<div class="portfolio-modal" id="portfolioModal">
    <div class="portfolio-modal__content">
        <div class="portfolio-modal__header">
            <img src="../assets/images/logo.svg" alt="Логотип" class="portfolio-modal__logo">
            <div class="portfolio-modal__header-text">Немного про эту работу</div>
            <button class="portfolio-modal__close" id="closeModal">&times;</button>
        </div>
        
        <div class="portfolio-modal__body">
            <div class="portfolio-modal__slider">
                <div class="portfolio-slider">
                    <div class="portfolio-slider__container">
                        <div class="portfolio-slider__slide" id="slideBefore">
                            <img src="" alt="До ремонта" class="portfolio-slider__image" id="modalBeforeImage">
                            <div class="portfolio-slider__caption">До ремонта</div>
                        </div>
                        <div class="portfolio-slider__slide" id="slideAfter">
                            <img src="" alt="После ремонта" class="portfolio-slider__image" id="modalAfterImage">
                            <div class="portfolio-slider__caption">После ремонта</div>
                        </div>
                    </div>
                    <div class="portfolio-slider__controls">
                        <button class="portfolio-slider__btn" id="prevSlide">&lt;</button>
                        <button class="portfolio-slider__btn" id="nextSlide">&gt;</button>
                    </div>
                    <div class="portfolio-slider__dots">
                        <div class="portfolio-slider__dot" data-slide="0"></div>
                        <div class="portfolio-slider__dot" data-slide="1"></div>
                    </div>
                </div>
            </div>
            <div class="portfolio-modal__info">
                <h2 class="portfolio-modal__title" id="modalProjectTitle"></h2>
                <div class="portfolio-modal__description" id="modalProjectDescription"></div>
                <div class="portfolio-modal__details">
                    <div class="portfolio-modal__detail-item">
                        <span class="portfolio-modal__detail-label">Дата завершения:</span>
                        <span class="portfolio-modal__detail-value" id="modalProjectDate"></span>
                    </div>
                    <div class="portfolio-modal__detail-item">
                        <span class="portfolio-modal__detail-label">Тип услуги:</span>
                        <span class="portfolio-modal__detail-value" id="modalProjectService"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('portfolioModal');
    const closeBtn = document.getElementById('closeModal');
    
    // Получаем данные о сервисах из базы
    const services = {
        1: 'Покраска стен',
        2: 'Замена напольного покрытия',
        3: 'Полный ремонт квартиры',
        4: 'Дизайн-проект интерьера',
        5: 'Укладка плитки',
        6: 'Установка натяжных потолков',
        7: 'Электромонтажные работы',
        8: 'Сантехнические работы',
        9: 'Штукатурка стен',
        10: 'Установка межкомнатных дверей',
        11: 'Монтаж гипсокартона',
        12: 'Утепление балкона',
        13: 'Установка кухонного гарнитура',
        14: 'Ремонт ванной комнаты'
    };
    
    // Функция для открытия модального окна
    function openProjectModal(item) {
        const title = item.getAttribute('data-title');
        const description = item.getAttribute('data-description');
        const beforeImage = item.getAttribute('data-before-image');
        const afterImage = item.getAttribute('data-after-image');
        const completedDate = item.getAttribute('data-completed-date');
        const serviceId = item.getAttribute('data-service-id');
        
        document.getElementById('modalProjectTitle').textContent = title;
        document.getElementById('modalProjectDescription').textContent = description;
        document.getElementById('modalProjectDate').textContent = completedDate;
        document.getElementById('modalProjectService').textContent = services[serviceId] || 'Не указано';
        
        // Устанавливаем изображения
        const modalBeforeImage = document.getElementById('modalBeforeImage');
        const modalAfterImage = document.getElementById('modalAfterImage');
        
        if (beforeImage) {
            modalBeforeImage.src = beforeImage;
            document.getElementById('slideBefore').style.display = 'block';
        } else {
            document.getElementById('slideBefore').style.display = 'none';
        }
        
        modalAfterImage.src = afterImage;
        
        // Сбрасываем слайдер
        currentSlide = 0;
        showSlide(currentSlide);
        
        // Показываем модальное окно
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    // Добавляем обработчики кликов для первоначальных карточек
    document.querySelectorAll('.portfolio-smartbuild__item').forEach(item => {
        item.addEventListener('click', function() {
            openProjectModal(this);
        });
    });
    
    // Закрытие модального окна
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    });
    
    // Закрытие при клике вне модального окна
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    // Логика слайдера
    let currentSlide = 0;
    const slides = document.querySelectorAll('.portfolio-slider__slide');
    const dots = document.querySelectorAll('.portfolio-slider__dot');
    
    function showSlide(n) {
        const beforeImage = document.getElementById('modalBeforeImage').src;
        const hasBeforeImage = beforeImage && beforeImage !== '';
        const totalSlides = hasBeforeImage ? slides.length : 1;
        
        n = (n + totalSlides) % totalSlides;
        
        slides.forEach((slide, index) => {
            if (!hasBeforeImage && index === 0) return;
            
            slide.classList.remove('active');
            if (index < dots.length) {
                dots[index].style.display = (index < totalSlides) ? 'block' : 'none';
            }
        });
        
        if (slides[n]) {
            slides[n].classList.add('active');
        }
        
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === n && index < totalSlides);
        });
        
        currentSlide = n;
    }
    
    // Обработчики для кнопок слайдера
    document.getElementById('nextSlide').addEventListener('click', function() {
        showSlide(currentSlide + 1);
    });
    
    document.getElementById('prevSlide').addEventListener('click', function() {
        showSlide(currentSlide - 1);
    });
    
    // Обработчики для точек слайдера
    dots.forEach((dot, index) => {
        dot.addEventListener('click', function() {
            showSlide(index);
        });
    });
    
    // Загрузка дополнительных проектов
    document.getElementById('loadMoreProjects').addEventListener('click', function() {
        const grid = document.querySelector('.portfolio-smartbuild__grid');
        const currentCount = grid.querySelectorAll('.portfolio-smartbuild__item').length;
        const loadMoreBtn = this;
        
        loadMoreBtn.disabled = true;
        loadMoreBtn.textContent = 'Загрузка...';
        
        fetch(`?limit=3&offset=${currentCount}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newProjects = doc.querySelectorAll('.portfolio-smartbuild__item');
                
                if (newProjects.length > 0) {
                    newProjects.forEach(project => {
                        grid.appendChild(project);
                        // Добавляем обработчики для новых карточек
                        project.addEventListener('click', function() {
                            openProjectModal(this);
                        });
                    });
                    
                    if (currentCount + newProjects.length >= totalProjects) {
                        const loadMoreContainer = document.querySelector('.portfolio-smartbuild__load-more');
                        loadMoreContainer.innerHTML = '<p style="text-align: center; color: #7A1E4C; font-weight: 500;">Все проекты загружены</p>';
                    } else {
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.textContent = 'Загрузить ещё';
                    }
                } else {
                    const loadMoreContainer = document.querySelector('.portfolio-smartbuild__load-more');
                    loadMoreContainer.innerHTML = '<p style="text-align: center; color: #7A1E4C; font-weight: 500;">Все проекты загружены</p>';
                }
            })
            .catch(error => {
                console.error('Ошибка при загрузке проектов:', error);
                loadMoreBtn.disabled = false;
                loadMoreBtn.textContent = 'Загрузить ещё';
                alert('Произошла ошибка при загрузке проектов. Пожалуйста, попробуйте позже.');
            });
    });
});
</script>

<?php
include_once __DIR__ . '/../includes/request_form.php';
include_once __DIR__ . '/../includes/footer.php';
?>