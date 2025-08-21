<?php
$title = htmlspecialchars($shop['name']) . ' - ' . APP_NAME;
ob_start();
?>

<div class="shop-details">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-4">
        <a href="/">Home</a> / 
        <a href="/shops">Shops</a> / 
        <span><?= htmlspecialchars($shop['name']) ?></span>
    </nav>

    <div class="shop-layout">
        <!-- Main Content -->
        <main class="shop-main">
            <!-- Image Gallery -->
            <div class="shop-gallery mb-4">
                <?php if (!empty($shop['images'])): ?>
                    <div class="gallery-main">
                        <img id="main-image" 
                             src="/storage/uploads/<?= htmlspecialchars($shop['images'][0]['path']) ?>" 
                             alt="<?= htmlspecialchars($shop['name']) ?>"
                             style="width: 100%; height: 400px; object-fit: cover; border-radius: var(--radius-lg);">
                    </div>
                    
                    <?php if (count($shop['images']) > 1): ?>
                        <div class="gallery-thumbnails mt-3">
                            <div class="flex" style="gap: 0.5rem; overflow-x: auto;">
                                <?php foreach ($shop['images'] as $index => $image): ?>
                                    <img class="thumbnail <?= $index === 0 ? 'active' : '' ?>" 
                                         src="/storage/uploads/<?= htmlspecialchars($image['path']) ?>" 
                                         alt="<?= htmlspecialchars($shop['name']) ?> - Image <?= $index + 1 ?>"
                                         data-full="/storage/uploads/<?= htmlspecialchars($image['path']) ?>"
                                         style="width: 80px; height: 80px; object-fit: cover; border-radius: var(--radius-md); cursor: pointer; border: 2px solid transparent;">
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="width: 100%; height: 400px; background: var(--gray-200); display: flex; align-items: center; justify-content: center; color: var(--gray-500); border-radius: var(--radius-lg);">
                        No Images Available
                    </div>
                <?php endif; ?>
            </div>

            <!-- Shop Info -->
            <div class="shop-info">
                <div class="flex flex--between mb-3" style="align-items: flex-start;">
                    <div>
                        <h1 class="mb-2"><?= htmlspecialchars($shop['name']) ?></h1>
                        <p class="text-muted">
                            <?= htmlspecialchars($shop['code']) ?> • 
                            <?= htmlspecialchars($shop['floor_name']) ?> • 
                            <?= htmlspecialchars($shop['category_name']) ?>
                        </p>
                    </div>
                    
                    <div class="text-right">
                        <?php
                        $statusClass = [
                            'available' => 'badge--success',
                            'reserved' => 'badge--warning',
                            'occupied' => 'badge--error'
                        ][$shop['status']] ?? 'badge--secondary';
                        ?>
                        <span class="badge <?= $statusClass ?> mb-2">
                            <?= ucfirst($shop['status']) ?>
                        </span>
                    </div>
                </div>

                <!-- Description -->
                <?php if ($shop['description']): ?>
                    <div class="shop-description mb-4">
                        <h3>Description</h3>
                        <p><?= nl2br(htmlspecialchars($shop['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Details Grid -->
                <div class="shop-details-grid mb-4">
                    <div class="grid grid--cols-2">
                        <div class="card">
                            <div class="card__body">
                                <h4 class="text-primary mb-2">Size</h4>
                                <p class="text-xl font-bold"><?= $shop['size_sqm'] ?>m²</p>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card__body">
                                <h4 class="text-primary mb-2">Monthly Rent</h4>
                                <p class="text-xl font-bold"><?= APP_CURRENCY_SYMBOL ?><?= number_format($shop['rent_monthly']) ?></p>
                            </div>
                        </div>
                        
                        <?php if ($shop['deposit_amount'] > 0): ?>
                            <div class="card">
                                <div class="card__body">
                                    <h4 class="text-primary mb-2">Security Deposit</h4>
                                    <p class="text-xl font-bold"><?= APP_CURRENCY_SYMBOL ?><?= number_format($shop['deposit_amount']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card">
                            <div class="card__body">
                                <h4 class="text-primary mb-2">Floor</h4>
                                <p class="text-xl font-bold"><?= htmlspecialchars($shop['floor_name']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                <?php if (!empty($shop['amenities'])): ?>
                    <div class="shop-amenities mb-4">
                        <h3 class="mb-3">Amenities</h3>
                        <div class="flex" style="gap: 0.5rem; flex-wrap: wrap;">
                            <?php foreach ($shop['amenities'] as $amenity): ?>
                                <span class="badge badge--info">
                                    <?= htmlspecialchars($amenity['name']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <!-- Sidebar -->
        <aside class="shop-sidebar">
            <!-- Action Card -->
            <div class="card mb-4">
                <div class="card__header">
                    <h3 class="card__title">Interested in this shop?</h3>
                </div>
                
                <div class="card__body">
                    <?php if ($shop['status'] === 'available'): ?>
                        <?php if (Auth::check()): ?>
                            <?php if (Auth::hasRole('tenant')): ?>
                                <a href="/tenant/applications?shop_id=<?= $shop['id'] ?>" 
                                   class="btn btn--primary" style="width: 100%; margin-bottom: 1rem;">
                                    Apply for This Shop
                                </a>
                            <?php else: ?>
                                <p class="text-muted text-center">
                                    Only tenants can apply for shops.
                                </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="/register" class="btn btn--primary" style="width: 100%; margin-bottom: 1rem;">
                                Register to Apply
                            </a>
                            <p class="text-center text-sm">
                                Already have an account? <a href="/login">Login here</a>
                            </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert--warning">
                            <p class="mb-0">This shop is currently <?= $shop['status'] ?> and not available for new applications.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="card mb-4">
                <div class="card__header">
                    <h3 class="card__title">Need More Information?</h3>
                </div>
                
                <div class="card__body">
                    <p class="text-sm text-muted mb-3">
                        Contact our leasing team for more details about this property.
                    </p>
                    
                    <div class="contact-info">
                        <p class="text-sm mb-2">
                            <strong>Email:</strong> leasing@megaplaza.com
                        </p>
                        <p class="text-sm mb-2">
                            <strong>Phone:</strong> +234-800-MEGA-PLAZA
                        </p>
                        <p class="text-sm">
                            <strong>Office Hours:</strong> Mon-Fri 9AM-6PM
                        </p>
                    </div>
                </div>
            </div>

            <!-- Share -->
            <div class="card">
                <div class="card__header">
                    <h3 class="card__title">Share This Shop</h3>
                </div>
                
                <div class="card__body">
                    <div class="flex" style="gap: 0.5rem;">
                        <button type="button" onclick="copyToClipboard()" class="btn btn--secondary btn--sm">
                            Copy Link
                        </button>
                        <a href="mailto:?subject=Check out this shop&body=<?= urlencode(Request::fullUrl()) ?>" 
                           class="btn btn--secondary btn--sm">
                            Email
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Similar Shops -->
    <?php if (!empty($similarShops)): ?>
        <section class="similar-shops mt-5">
            <h2 class="mb-4">Similar Shops</h2>
            
            <div class="grid grid--cols-3">
                <?php foreach ($similarShops as $similar): ?>
                    <div class="shop-card card">
                        <div class="shop-card__image">
                            <?php if ($similar['cover_image']): ?>
                                <img src="/storage/uploads/<?= htmlspecialchars($similar['cover_image']) ?>" 
                                     alt="<?= htmlspecialchars($similar['name']) ?>"
                                     style="width: 100%; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 100%; height: 150px; background: var(--gray-200); display: flex; align-items: center; justify-content: center; color: var(--gray-500);">
                                    No Image
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card__body">
                            <h4 class="shop-card__title">
                                <a href="/shops/<?= $similar['id'] ?>"><?= htmlspecialchars($similar['name']) ?></a>
                            </h4>
                            
                            <p class="text-sm text-muted mb-2">
                                <?= htmlspecialchars($similar['code']) ?> • <?= $similar['size_sqm'] ?>m²
                            </p>
                            
                            <p class="text-lg font-bold text-primary">
                                <?= APP_CURRENCY_SYMBOL ?><?= number_format($similar['rent_monthly']) ?>/month
                            </p>
                        </div>
                        
                        <div class="card__footer">
                            <a href="/shops/<?= $similar['id'] ?>" class="btn btn--primary btn--sm" style="width: 100%;">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<!-- Styles -->
<style>
.breadcrumb {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.breadcrumb a {
    color: var(--primary-color);
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.shop-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
}

.shop-main {
    min-width: 0;
}

.shop-sidebar {
    position: sticky;
    top: 1rem;
    height: fit-content;
}

.thumbnail {
    transition: border-color var(--transition-fast);
}

.thumbnail:hover,
.thumbnail.active {
    border-color: var(--primary-color) !important;
}

.shop-details-grid .card {
    text-align: center;
}

.shop-card {
    transition: transform var(--transition-fast), box-shadow var(--transition-fast);
}

.shop-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.shop-card__image {
    overflow: hidden;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}

.shop-card__title a {
    color: var(--gray-900);
    text-decoration: none;
    font-weight: 600;
}

.shop-card__title a:hover {
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .shop-layout {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .shop-sidebar {
        position: static;
        order: -1;
    }
    
    .shop-details-grid .grid--cols-2 {
        grid-template-columns: 1fr;
    }
    
    .similar-shops .grid--cols-3 {
        grid-template-columns: 1fr;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .shop-layout {
        grid-template-columns: 1fr 300px;
    }
    
    .similar-shops .grid--cols-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image gallery functionality
    const mainImage = document.getElementById('main-image');
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            // Update main image
            mainImage.src = this.dataset.full;
            
            // Update active thumbnail
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

// Copy link functionality
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        MegaPlaza.showAlert('Link copied to clipboard!', 'success');
    }).catch(function() {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = window.location.href;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        MegaPlaza.showAlert('Link copied to clipboard!', 'success');
    });
}
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/Views/layout.php';
?>
