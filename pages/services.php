<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($pdo)) {
    die("Ошибка: Подключение к базе данных не установлено");
}

include_once __DIR__ . '/../includes/header.php';

// Получаем параметры
$selectedCategories = isset($_GET['categories']) ? (array)$_GET['categories'] : [];
$minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 100000;
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$isFilterApplied = isset($_GET['filter_applied']);
$excludeIds = isset($_GET['exclude_ids']) ? explode(',', $_GET['exclude_ids']) : [];

function renderServiceItem($service) {
    $html = '<div class="service__item service__item--3x2" data-id="'.$service['id'].'">';
    if (!empty($service['image'])) {
        $html .= '<div class="service__image service__image--3x2">';
        $html .= '<img src="../assets/images/' . htmlspecialchars($service['image']) . '" alt="' . htmlspecialchars($service['title']) . '">';
        $html .= '</div>';
    }
    $html .= '<h3 class="service__title--3x2">' . htmlspecialchars($service['title']) . '</h3>';
    $html .= '<p class="price price--3x2">' . number_format($service['price'], 0, '', ' ') . ' ₽</p>';
    $html .= '<p class="old-price old-price--3x2">' . number_format($service['price'] * 1.4, 0, '', ' ') . ' ₽</p>';
    $html .= '<a href="#" class="btn btn--3x2 request-btn" data-service-id="'.$service['id'].'" data-service-title="'.htmlspecialchars($service['title']).'">Заказать</a>';
    $html .= '</div>';
    return $html;
}
?>

<style>
    .load-more-container {
        text-align: center;
        margin: 30px 0;
    }

    .load-more-btn {
       display: inline-block;
       padding: 5px;
       background-color:#7A1E4C;
       color:white;
       text-decoration: none;
       border: solid;
       border-radius: 4px;
       font-weight: 1500;
       transition: background-color 0.3s;
       border-width: 2px;
       border-color: #7A1E4C;
       cursor: pointer;
       transition: all 0.3s;
       width: 240px;
       height: 40px;
    }

    .load-more-btn:hover {
         color: #7A1E4C;
         border-color:#7A1E4C;
         transform: translateY(-2px);
         background-color:transparent;
    }
    
    .load-more-btn.loading {
        background-color: #7f8c8d;
        cursor: not-allowed;
    }
    .service__item {
        width: 100%; 
    }
    .service__image img {
        min-width: 100%; 
        min-height: 100%; 
        object-fit: cover;
        border-radius: 4px;
    }

    .apply-filters-btn {
        display: block;
        width: 100%;
        padding: 12px;
        background-color: #7A1E4C;
        color: white;
        border: none;
        border-radius: 4px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
    }

    .apply-filters-btn:hover {
        background-color: #6a1a44;
        transform: translateY(-2px);
    }

    .filter-option input[type="radio"] {
        display: none;
    }
    
    .custom-radio {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #7A1E4C;
        border-radius: 50%;
        margin-right: 8px;
        position: relative;
        vertical-align: middle;
    }
    
    .custom-radio.checked::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 8px;
        height: 8px;
        background-color: #7A1E4C;
        border-radius: 50%;
    }
    
    .no-results {
        text-align: center;
        padding: 20px;
        font-size: 18px;
        color: #666;
        grid-column: 1 / -1;
    }

  
</style>

<div class="main-content">
   <aside class="filters">
    <form id="filterForm" method="GET">
        <input type="hidden" name="filter_applied" value="1">
        
        <!-- Тип работы -->
        <div class="filter-section">
            <h3>ТИП РАБОТЫ</h3>
            <div class="filter-options">
                <?php
                $categories = $pdo->query("SELECT * FROM service_categories")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $category) {
                    $checked = in_array($category['id'], $selectedCategories) ? 'checked' : '';
                    echo '<div class="filter-option">
                        <input type="checkbox" id="filter-'.$category['id'].'" name="categories[]" value="'.$category['id'].'" class="filter-checkbox" '.$checked.'>
                        <label for="filter-'.$category['id'].'">
                            <span class="custom-checkbox '.($checked ? 'checked' : '').'"></span>
                            '.htmlspecialchars($category['name']).'
                        </label>
                    </div>';
                }
                ?>
            </div>
        </div>

        <!-- Цена -->
        <div class="filter-section">
            <div class="filter-prise">
                <h3>ЦЕНА</h3>
                <div class="price-filter">
                    <div class="price-inputs">
                        <div class="price-input">
                            <span>от</span>
                            <input type="number" id="minPrice" name="min_price" class="price-input-field" placeholder="0" min="0" max="100000" value="<?= $minPrice ?>">
                        </div>
                        <div class="price-input">
                            <span>до</span>
                            <input type="number" id="maxPrice" name="max_price" class="price-input-field" placeholder="100000" min="0" max="100000" value="<?= $maxPrice ?>">
                        </div>
                    </div>
                    <div class="price-slider-container">
                        <div class="price-slider">
                            <div class="price-slider-track"></div>
                            <input type="range" min="0" max="100000" value="<?= $minPrice ?>" class="price-slider-min" id="priceRangeMin">
                            <input type="range" min="0" max="100000" value="<?= $maxPrice ?>" class="price-slider-max" id="priceRangeMax">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Сортировка -->
        <div class="filter-section">
            <h3>СОРТИРОВКА</h3>
            <div class="filter-options">
                <div class="filter-option">
                    <input type="radio" id="sort-none" name="sort" class="filter-radio" value="" <?= empty($sort) ? 'checked' : '' ?>>
                    <label for="sort-none">
                        <span class="custom-radio <?= empty($sort) ? 'checked' : '' ?>"></span>
                        БЕЗ СОРТИРОВКИ
                    </label>
                </div>
                
                <div class="filter-option">
                    <input type="radio" id="sort-1" name="sort" class="filter-radio" value="popular" <?= $sort === 'popular' ? 'checked' : '' ?>>
                    <label for="sort-1">
                        <span class="custom-radio <?= $sort === 'popular' ? 'checked' : '' ?>"></span>
                        ПОПУЛЯРНЫЕ
                    </label>
                </div>
                
                <div class="filter-option">
                    <input type="radio" id="sort-2" name="sort" class="filter-radio" value="expensive" <?= $sort === 'expensive' ? 'checked' : '' ?>>
                    <label for="sort-2">
                        <span class="custom-radio <?= $sort === 'expensive' ? 'checked' : '' ?>"></span>
                        ДОРОГИЕ
                    </label>
                </div>
                
                <div class="filter-option">
                    <input type="radio" id="sort-3" name="sort" class="filter-radio" value="cheap" <?= $sort === 'cheap' ? 'checked' : '' ?>>
                    <label for="sort-3">
                        <span class="custom-radio <?= $sort === 'cheap' ? 'checked' : '' ?>"></span>
                        ДЕШЕВЫЕ
                    </label>
                </div>
            </div>
        </div>
        
        <div class="filter-section">
            <button type="submit" class="apply-filters-btn">Применить фильтры</button>
        </div>
    </form>
</aside>

    <div class="services__container">
        <section class="services">
            <div class="container">
                <h1 class="about-title">Наши услуги</h1>
                <div class="services__grid services__grid--3x2" id="servicesGrid">
                    <?php
                    $sql = "SELECT DISTINCT s.* FROM services s";
                    
                    if (!empty($selectedCategories)) {
                        $sql .= " JOIN service_to_category stc ON s.id = stc.service_id 
                                 WHERE stc.category_id IN (".implode(',', array_map('intval', $selectedCategories)).")";
                        $where = " AND";
                    } else {
                        $where = " WHERE";
                    }
                    
                    $sql .= $where." s.price BETWEEN :min_price AND :max_price";
                    
                    if (!empty($excludeIds)) {
                        $sql .= " AND s.id NOT IN (".implode(',', array_map('intval', $excludeIds)).")";
                    }
                    
                    switch ($sort) {
                        case 'expensive':
                            $sql .= " ORDER BY s.price DESC";
                            break;
                        case 'cheap':
                            $sql .= " ORDER BY s.price ASC";
                            break;
                        case 'popular':
                            $sql .= " ORDER BY s.id DESC";
                            break;
                        default:
                            // Без сортировки
                            break;
                    }
                    
                    if (!$isFilterApplied) {
                        $sql .= " LIMIT :limit";
                        $limit = 6;
                    }
                    
                    $stmt = $pdo->prepare($sql);
                    
                    $stmt->bindValue(':min_price', $minPrice, PDO::PARAM_INT);
                    $stmt->bindValue(':max_price', $maxPrice, PDO::PARAM_INT);
                    
                    if (!$isFilterApplied) {
                        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    }
                    
                    $stmt->execute();
                    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($services)) {
                        echo '<p class="no-results">Услуги по выбранным фильтрам не найдены.</p>';
                    } else {
                        foreach ($services as $service) {
                            echo renderServiceItem($service);
                        }
                    }
                    ?>
                </div>
                
                <?php if (!$isFilterApplied && !empty($services) && count($services) >= $limit): ?>
                <div class="load-more-container">
                    <button id="loadMoreBtn" class="load-more-btn" data-offset="1" data-loaded-ids="<?= implode(',', array_column($services, 'id')) ?>">Загрузить ещё</button>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<!-- Модальное окно для заявки -->
<div id="requestModal" class="modal">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="section-title">Оставить заявку на ремонт</h2>
            <form id="repairRequestForm">
                <div class="form-group-header">
                    <label for="service">Выберите услугу:</label>
                    <select id="service" name="service" required>
                        <option value="" disabled selected>-- Выберите услугу --</option>
                        <?php
                      
                        // Запрос для получения активных услуг
                        $servicesQuery = $db->query("SELECT id, title FROM services WHERE is_active = 1");
                        $services = $servicesQuery->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($services as $service) {
                            echo '<option value="' . htmlspecialchars($service['id']) . '">' . htmlspecialchars($service['title']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group-header">
                    <label for="promotion">Выберите акцию (если есть):</label>
                    <select id="promotion" name="promotion">
                        <option value="" selected>-- Без акции --</option>
                        <?php
                        // Запрос для получения активных акций
                        $promotionsQuery = $db->query("SELECT id, title FROM promotions WHERE is_active = 1 AND end_date >= CURDATE()");
                        $promotions = $promotionsQuery->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($promotions as $promotion) {
                            echo '<option value="' . htmlspecialchars($promotion['id']) . '">' . htmlspecialchars($promotion['title']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group-header">
                    <label for="address">Адрес объекта:</label>
                    <input type="text" id="address" name="address" required placeholder="Введите адрес, где нужно выполнить работы">
                </div>
                
                <div class="form-group-header">
                    <label for="description">Подробное описание работ:</label>
                    <textarea id="description" name="description" required placeholder="Опишите, какие именно работы нужно выполнить, ваши пожелания и требования"></textarea>
                </div>
                
                <button type="submit" class="submit_btn1">Отправить заявку</button>
            </form>
        </div>
    <?php else: ?>
        <div class="modal-content">
            <p>Для подачи заявки необходимо <a href="../ad/login.php">войти</a></p>
        </div>
    <?php endif; ?>
</div>

<?php
include_once __DIR__ . '/../includes/request_form.php';
include_once __DIR__ . '/../includes/footer.php';

if (isset($_GET['load_more']) && $_GET['load_more'] === 'true' 
    && isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
{
    $selectedCategories = isset($_GET['categories']) ? (array)$_GET['categories'] : [];
    $minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
    $maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 100000;
    $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 1;
    $limit = 6;
    $excludeIds = isset($_GET['exclude_ids']) ? explode(',', $_GET['exclude_ids']) : [];
    
    $sql = "SELECT s.* FROM services s";
    
    if (!empty($selectedCategories)) {
        $sql .= " JOIN service_to_category stc ON s.id = stc.service_id 
                 WHERE stc.category_id IN (".implode(',', array_map('intval', $selectedCategories)).")";
        $where = " AND";
    } else {
        $where = " WHERE";
    }
    
    $sql .= $where." s.price BETWEEN :min_price AND :max_price";
    
    if (!empty($excludeIds)) {
        $sql .= " AND s.id NOT IN (".implode(',', array_map('intval', $excludeIds)).")";
    }
    
    switch ($sort) {
        case 'expensive':
            $sql .= " ORDER BY s.price DESC";
            break;
        case 'cheap':
            $sql .= " ORDER BY s.price ASC";
            break;
        case 'popular':
            $sql .= " ORDER BY s.id DESC";
            break;
        default:
            // Без сортировки
            break;
    }
    
    $sql .= " LIMIT :limit";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindValue(':min_price', $minPrice, PDO::PARAM_INT);
    $stmt->bindValue(':max_price', $maxPrice, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    
    $stmt->execute();
    
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($services)) {
        foreach ($services as $service) {
            echo renderServiceItem($service);
        }
    } else {
        echo '<p class="no-results">Больше услуг не найдено</p>';
    }
    
    exit;
}
?>

<script>
// Обработка чекбоксов
document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const label = this.nextElementSibling;
        const customBox = label.querySelector('.custom-checkbox');
        customBox.classList.toggle('checked', this.checked);
    });
});

// Обработка радио-кнопок
document.querySelectorAll('.filter-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.custom-radio').forEach(r => r.classList.remove('checked'));
        if (this.checked) {
            this.nextElementSibling.querySelector('.custom-radio').classList.add('checked');
        }
    });
});

// Обработка слайдера цены
const minPriceInput = document.getElementById('minPrice');
const maxPriceInput = document.getElementById('maxPrice');
const priceRangeMin = document.getElementById('priceRangeMin');
const priceRangeMax = document.getElementById('priceRangeMax');
const priceSliderTrack = document.querySelector('.price-slider-track');
const filterForm = document.getElementById('filterForm');

// Устанавливаем начальное положение ползунка
updateSlider();

// Обработчики для полей ввода
[minPriceInput, maxPriceInput].forEach(input => {
    input.addEventListener('input', function() {
        let minVal = parseInt(minPriceInput.value) || 0;
        let maxVal = parseInt(maxPriceInput.value) || 100000;
        
        if (minVal < 0) minVal = 0;
        if (maxVal > 100000) maxVal = 100000;
        if (minVal > maxVal) {
            if (this === minPriceInput) {
                minVal = maxVal;
                minPriceInput.value = maxVal;
            } else {
                maxVal = minVal;
                maxPriceInput.value = minVal;
            }
        }
        
        priceRangeMin.value = minVal;
        priceRangeMax.value = maxVal;
        updateSlider();
    });
});

// Обработчики для ползунков
[priceRangeMin, priceRangeMax].forEach(slider => {
    slider.addEventListener('input', function() {
        let minVal = parseInt(priceRangeMin.value);
        let maxVal = parseInt(priceRangeMax.value);
        
        if (minVal > maxVal) {
            if (this === priceRangeMin) {
                priceRangeMax.value = minVal;
                maxVal = minVal;
            } else {
                priceRangeMin.value = maxVal;
                minVal = maxVal;
            }
        }
        
        minPriceInput.value = minVal;
        maxPriceInput.value = maxVal;
        updateSlider();
    });
});

function updateSlider() {
    const minVal = parseInt(priceRangeMin.value);
    const maxVal = parseInt(priceRangeMax.value);
    const minPercent = (minVal / 100000) * 100;
    const maxPercent = (maxVal / 100000) * 100;
    
    priceSliderTrack.style.left = minPercent + "%";
    priceSliderTrack.style.width = (maxPercent - minPercent) + "%";
}

// Функция для загрузки дополнительных услуг
document.getElementById('loadMoreBtn')?.addEventListener('click', function() {
    const btn = this;
    const offset = parseInt(btn.dataset.offset);
    const limit = 6;
    const currentLoadedIds = btn.dataset.loadedIds ? btn.dataset.loadedIds.split(',') : [];
    
    const formData = new FormData(filterForm);
    const params = new URLSearchParams();
    
    formData.forEach((value, key) => {
        if (key === 'categories[]') {
            formData.getAll(key).forEach(val => params.append(key, val));
        } else if (value !== '' || key === 'sort') {
            params.append(key, value);
        }
    });
    
    // Добавляем ID уже загруженных товаров
    params.append('exclude_ids', currentLoadedIds.join(','));
    params.append('offset', offset);
    params.append('load_more', 'true');
    
    btn.classList.add('loading');
    btn.textContent = 'Загрузка...';
    
    fetch('?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newItems = doc.getElementById('servicesGrid')?.innerHTML;
        
        if (newItems && newItems.trim() !== '') {
            document.getElementById('servicesGrid').insertAdjacentHTML('beforeend', newItems);
            btn.dataset.offset = offset + 1;
            
            // Обновляем список загруженных ID
            const newIds = [];
            doc.querySelectorAll('.service__item').forEach(item => {
                const id = item.dataset.id;
                if (id) newIds.push(id);
            });
            
            btn.dataset.loadedIds = [...currentLoadedIds, ...newIds].join(',');
            
            // Скрываем кнопку, если больше нет товаров
            if (doc.querySelector('.no-results') || newIds.length < limit) {
                btn.style.display = 'none';
            }
        } else {
            btn.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Произошла ошибка при загрузке услуг. Пожалуйста, попробуйте еще раз.');
    })
    .finally(() => {
        btn.classList.remove('loading');
        btn.textContent = 'Загрузить ещё';
    });
});

// Обработка формы фильтрации
filterForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const params = new URLSearchParams();
    
    formData.forEach((value, key) => {
        if (key === 'categories[]') {
            formData.getAll(key).forEach(val => params.append(key, val));
        } else if (value !== '' || key === 'sort') {
            params.append(key, value);
        }
    });
    
    fetch('?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const servicesGrid = doc.getElementById('servicesGrid');
        const loadMoreContainer = doc.querySelector('.load-more-container');
        
        if (servicesGrid) {
            document.getElementById('servicesGrid').innerHTML = servicesGrid.innerHTML;
            if (loadMoreContainer) {
                document.querySelector('.load-more-container').innerHTML = loadMoreContainer.innerHTML;
            } else {
                document.querySelector('.load-more-container')?.remove();
            }
        }
        
        history.pushState(null, '', '?' + params.toString());
    })
    .catch(error => {
        console.error('Error:', error);
        this.submit();
    });
});

window.addEventListener('popstate', function() {
    location.reload();
});

// Обработка модального окна заявок
document.querySelectorAll('.request-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        if(<?php echo isset($_SESSION['user_id']) ? 'false' : 'true'; ?>) {
            window.location.href = '../ad/login.php';
            return;
        }
        
        // Получаем данные об услуге из атрибутов кнопки
        const serviceId = this.getAttribute('data-service-id');
        const serviceTitle = this.getAttribute('data-service-title');
        
        // Находим модальное окно и поле выбора услуги
        const modal = document.getElementById("requestModal");
        const serviceSelect = modal.querySelector("#service");
        
        // Устанавливаем выбранную услугу
        for (let option of serviceSelect.options) {
            if (option.value === serviceId) {
                option.selected = true;
                break;
            }
        }
        
        // Показываем модальное окно
        modal.style.display = "block";
        document.body.style.overflow = "hidden";
        
        // Обработчик закрытия модального окна
        const span = modal.getElementsByClassName("close")[0];
        span.onclick = function() {
            modal.style.display = "none";
            document.body.style.overflow = "auto";
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                document.body.style.overflow = "auto";
            }
        }
    });
});

// Обработка формы заявки
document.getElementById("repairRequestForm")?.addEventListener("submit", function(e) {
    e.preventDefault();
    
    fetch('../includes/header.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(new FormData(this)) + '&repair_request=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            document.getElementById("requestModal").style.display = "none";
            document.body.style.overflow = "auto";
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>