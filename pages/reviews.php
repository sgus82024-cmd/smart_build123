<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($pdo)) {
    die("Ошибка: Подключение к базе данных не установлено");
}

include_once __DIR__ . '/../includes/header.php';

// Получаем номер страницы из GET-параметра, по умолчанию 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 3;
$offset = ($page - 1) * $limit;

// Запрос для получения общего количества одобренных отзывов
$total_stmt = $pdo->query("SELECT COUNT(*) FROM reviews WHERE is_approved = 1");
$total = $total_stmt->fetchColumn();
$total_pages = ceil($total / $limit);

// Запрос для получения отзывов с пагинацией
$query = "SELECT r.*, u.first_name 
          FROM reviews r 
          JOIN users u ON r.user_id = u.id 
          WHERE r.is_approved = 1 
          ORDER BY r.created_at DESC 
          LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?> 

<section class="reviews">
    <div class="container">
        <h1 class="about-title">ОТЗЫВЫ ДОВОЛЬНЫХ КЛИЕНТОВ</h1>
        <div class="reviews__grid" id="reviews-grid">
            <?php
            // Вывод каждого отзыва
            foreach ($reviews as $review) {
                echo '<div class="review__item">';
                
                // Блок с рейтингом
                echo '<div class="review__rating">';
                for ($i = 1; $i <= 5; $i++) {
                    $starClass = $i <= $review['rating'] ? 'star-filled' : 'star-empty';
                    echo '<span class="star ' . $starClass . '">★</span>';
                }
                echo '</div>';
                echo '<div class="review__text">';
                echo '<p>' . htmlspecialchars($review['comment']) . '</p>';
                echo '</div>';
                echo '<div class="review__author">';
                echo '<p>' . htmlspecialchars($review['first_name']) . '</p>';
                echo '</div>';
                
                echo '</div>';
            }
            ?>
        </div>
        
        <div class="section-footer" id="load-more-container">
            <?php if ($page < $total_pages): ?>
                <a href="#" class="btn" id="load-more-btn">
                   
                    Загрузить ещё
                </a>
            <?php else: ?>
                <p>Все отзывы загружены</p>
            <?php endif; ?>
            
            </a>
        </div>
    </div>
</section>

<!-- В остальной части кода все остается без изменений, меняем только JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const reviewsGrid = document.getElementById('reviews-grid');
    const loadMoreContainer = document.getElementById('load-more-container');
    
    // Начинаем с текущей страницы из PHP
    let currentPage = <?php echo $page; ?>;
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            currentPage++; // Увеличиваем номер страницы при каждом клике
            
            fetch(`reviews.php?page=${currentPage}`)
                .then(response => response.text())
                .then(html => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    
                    const newItems = tempDiv.querySelector('#reviews-grid').innerHTML;
                    const newLoadMoreContainer = tempDiv.querySelector('#load-more-container');
                    
                    reviewsGrid.innerHTML += newItems;
                    
                    // Полностью заменяем контейнер, чтобы обновить состояние кнопки
                    loadMoreContainer.innerHTML = newLoadMoreContainer.innerHTML;
                    
                    // Если кнопка еще существует, нужно заново добавить обработчик
                    const newLoadMoreBtn = document.getElementById('load-more-btn');
                    if (newLoadMoreBtn) {
                        newLoadMoreBtn.addEventListener('click', arguments.callee);
                    }
                })
                .catch(error => console.error('Ошибка:', error));
        });
    }
});
</script>


<?php
include_once __DIR__ . '/../includes/reviews_form.php';
include_once __DIR__ . '/../includes/footer.php';
?>