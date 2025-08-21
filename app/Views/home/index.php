<?php
$title = APP_NAME . ' - Premium Shop Rentals';
ob_start();
?>

<!-- Hero Section -->
<section class="hero" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; padding: 4rem 0; margin: -2rem -1rem 3rem -1rem;">
    <div class="container text-center">
        <h1 style="font-size: 3rem; margin-bottom: 1rem;">Welcome to <?= APP_NAME ?></h1>
        <p style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.9;">
            Find your perfect commercial space in our premium shopping plaza
        </p>
        <div class="flex flex--center" style="gap: 1rem; flex-wrap: wrap;">
            <a href="/shops" class="btn btn--secondary btn--lg">Browse Available Shops</a>
            <?php if (Auth::guest()): ?>
                <a href="/register" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">Get Started</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="stats mb-5">
    <div class="grid grid--cols-4">
        <div class="card text-center">
            <div class="card__body">
                <h3 class="text-primary" style="font-size: 2rem; margin-bottom: 0.5rem;">
                    <?= $stats['total_shops'] ?>
                </h3>
                <p class="text-muted mb-0">Total Shops</p>
            </div>
        </div>
        <div class="card text-center">
            <div class="card__body">
                <h3 class="text-success" style="font-size: 2rem; margin-bottom: 0.5rem;">
                    <?= $stats['available_shops'] ?>
                </h3>
                <p class="text-muted mb-0">Available Now</p>
            </div>
        </div>
        <div class="card text-center">
            <div class="card__body">
                <h3 class="text-warning" style="font-size: 2rem; margin-bottom: 0.5rem;">
                    <?= APP_CURRENCY_SYMBOL ?><?= number_format($stats['avg_rent']) ?>
                </h3>
                <p class="text-muted mb-0">Average Rent</p>
            </div>
        </div>
        <div class="card text-center">
            <div class="card__body">
                <h3 class="text-info" style="font-size: 2rem; margin-bottom: 0.5rem;">
                    <?= round($stats['avg_size'], 1) ?>m²
                </h3>
                <p class="text-muted mb-0">Average Size</p>
            </div>
        </div>
    </div>
</section>

<!-- Quick Filters -->
<section class="quick-filters mb-5">
    <div class="card">
        <div class="card__header">
            <h3 class="card__title">Browse by Category</h3>
        </div>
        <div class="card__body">
            <div class="flex" style="gap: 1rem; flex-wrap: wrap; justify-content: center;">
                <a href="/shops" class="btn btn--secondary">All Shops</a>
                <?php foreach ($categories as $category): ?>
                    <a href="/shops?category=<?= $category['id'] ?>" class="btn btn--secondary">
                        <?= htmlspecialchars($category['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Featured Shops -->
<section class="featured-shops">
    <div class="flex flex--between mb-4" style="align-items: center;">
        <h2>Featured Available Shops</h2>
        <a href="/shops" class="btn btn--primary">View All Shops</a>
    </div>
    
    <?php if (empty($featuredShops)): ?>
        <div class="card">
            <div class="card__body text-center">
                <p class="text-muted">No shops available at the moment. Please check back later.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid--cols-4">
            <?php foreach ($featuredShops as $shop): ?>
                <div class="shop-card card">
                    <div class="shop-card__image">
                        <?php if ($shop['cover_image']): ?>
                            <img src="/storage/uploads/<?= htmlspecialchars($shop['cover_image']) ?>" 
                                 alt="<?= htmlspecialchars($shop['name']) ?>"
                                 style="width: 100%; height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: var(--gray-200); display: flex; align-items: center; justify-content: center; color: var(--gray-500);">
                                No Image
                            </div>
                        <?php endif; ?>
                        
                        <div class="shop-card__status">
                            <span class="badge badge--success">Available</span>
                        </div>
                    </div>
                    
                    <div class="card__body">
                        <h4 class="shop-card__title">
                            <a href="/shops/<?= $shop['id'] ?>"><?= htmlspecialchars($shop['name']) ?></a>
                        </h4>
                        
                        <div class="shop-card__details">
                            <p class="text-sm text-muted mb-1">
                                <?= htmlspecialchars($shop['code']) ?> • <?= htmlspecialchars($shop['floor_name']) ?>
                            </p>
                            <p class="text-sm text-muted mb-2">
                                <?= htmlspecialchars($shop['category_name']) ?> • <?= $shop['size_sqm'] ?>m²
                            </p>
                        </div>
                        
                        <div class="shop-card__price">
                            <span class="text-lg font-bold text-primary">
                                <?= APP_CURRENCY_SYMBOL ?><?= number_format($shop['rent_monthly']) ?>/month
                            </span>
                        </div>
                    </div>
                    
                    <div class="card__footer">
                        <a href="/shops/<?= $shop['id'] ?>" class="btn btn--primary" style="width: 100%;">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Additional CSS for shop cards -->
<style>
.hero {
    border-radius: var(--radius-lg);
}

.shop-card {
    position: relative;
    transition: transform var(--transition-fast), box-shadow var(--transition-fast);
}

.shop-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.shop-card__image {
    position: relative;
    overflow: hidden;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}

.shop-card__status {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
}

.shop-card__title a {
    color: var(--gray-900);
    text-decoration: none;
    font-weight: 600;
}

.shop-card__title a:hover {
    color: var(--primary-color);
}

.shop-card__details {
    margin: 0.5rem 0;
}

.shop-card__price {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

@media (max-width: 768px) {
    .hero h1 {
        font-size: 2rem !important;
    }
    
    .hero p {
        font-size: 1rem !important;
    }
    
    .stats .grid--cols-4 {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .featured-shops .grid--cols-4 {
        grid-template-columns: 1fr;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .featured-shops .grid--cols-4 {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<?php
$content = ob_get_clean();
include APP_PATH . '/Views/layout.php';
?>
