<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($pdo)) {
    die("Ошибка: Подключение к базе данных не установлено");
}

include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/slider_for_promotions.php'; 

// Получаем номер страницы из GET-параметра, по умолчанию 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 4;
$offset = ($page - 1) * $limit;

// Запрос для получения общего количества акций
$total_stmt = $pdo->query("SELECT COUNT(*) FROM promotions WHERE is_active = 1");
$total = $total_stmt->fetchColumn();
$total_pages = ceil($total / $limit);

// Запрос для получения акций с пагинацией
$stmt = $pdo->prepare("SELECT * FROM promotions WHERE is_active = 1 LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
?>

<section class="promotions">
    <div class="container">
        <h2 class="section-title">Акции</h2>
        <div class="promotions__grid" id="promotions-grid">
            <?php
            while ($promo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="promo__item">';
                {
                    ?>
                <svg width="97" height="96" viewBox="0 0 97 96" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect x="0.5" width="96" height="96" fill="url(#pattern0_237_3602)"/>
<defs>
<pattern id="pattern0_237_3602" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_237_3602" transform="scale(0.0104167)"/>
</pattern>
<image id="image0_237_3602" width="96" height="96" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>

                <?php
                }
                if (!empty($promo['image'])) {
                    echo '<div class="service__image">';
                    echo '<img src="../assets/images/' . htmlspecialchars($promo['image']) . '" alt="' . htmlspecialchars($promo['title']) . '">';
                    echo '</div>';
                }
                echo '<h3>' . htmlspecialchars($promo['title']) . '</h3>';
                echo '<p class="price">' . number_format(1000 - (1000 * $promo['discount_value'] / 100), 0, '', ' ') . ' руб./м2</p>';
                echo '<p class="old-price">' . number_format(1000, 0, '', ' ') . ' руб./м2</p>';
                echo '<a href="../pages/services.php?id=' . $promo['id'] . '" class="btn">Заказать</a>';
                echo '</div>';
            }
            ?>
        </div>
        
        <div class="section-footer" id="load-more-container">
            <?php if ($page < $total_pages): ?>
                <a href="#" class="btn" id="load-more-btn">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 0V20M20 10H0" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Загрузить ещё
                </a>
            <?php else: ?>
                <p>Все акции загружены</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const promotionsGrid = document.getElementById('promotions-grid');
    const loadMoreContainer = document.getElementById('load-more-container');
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const currentPage = <?php echo $page; ?>;
            const nextPage = currentPage + 1;
            
            fetch(`promotions.php?page=${nextPage}`)
                .then(response => response.text())
                .then(html => {
                    // Создаем временный элемент для парсинга HTML
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    
                    // Находим новые элементы и кнопку загрузки
                    const newItems = tempDiv.querySelector('#promotions-grid').innerHTML;
                    const newLoadMoreContainer = tempDiv.querySelector('#load-more-container');
                    
                    // Добавляем новые элементы
                    promotionsGrid.innerHTML += newItems;
                    
                    // Обновляем кнопку загрузки
                    loadMoreContainer.innerHTML = newLoadMoreContainer.innerHTML;
                })
                .catch(error => console.error('Ошибка:', error));
        });
    }
});
</script>

<?php
include_once __DIR__ . '/../includes/request_form.php';
include_once __DIR__ . '/../includes/footer.php';
?>