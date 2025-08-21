<?php
$title = 'Browse Shops - ' . APP_NAME;
ob_start();
?>

<div class="shops-catalog">
    <!-- Page Header -->
    <div class="flex flex--between mb-4" style="align-items: center;">
        <div>
            <h1>Browse Shops</h1>
            <p class="text-muted">
                Showing <?= count($shops) ?> of <?= $pagination['totalItems'] ?> shops
            </p>
        </div>
        
        <div class="flex" style="gap: 1rem; align-items: center;">
            <button type="button" id="toggle-filters" class="btn btn--secondary">
                <span>Filters</span>
            </button>
        </div>
    </div>

    <div class="shops-layout">
        <!-- Filters Sidebar -->
        <aside class="filters-sidebar" id="filters-sidebar">
            <div class="card">
                <div class="card__header">
                    <h3 class="card__title">Filter Shops</h3>
                </div>
                
                <div class="card__body">
                    <form method="GET" action="/shops" id="filters-form">
                        <!-- Search -->
                        <div class="form__group">
                            <label for="search" class="form__label">Search</label>
                            <input 
                                type="text" 
                                id="search" 
                                name="search" 
                                class="form__input" 
                                placeholder="Shop name, code, or description..."
                                value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                                data-search="shops"
                            >
                        </div>
                        
                        <!-- Status -->
                        <div class="form__group">
                            <label for="status" class="form__label">Status</label>
                            <select id="status" name="status" class="form__select">
                                <option value="">All Statuses</option>
                                <option value="available" <?= ($filters['status'] ?? '') === 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="reserved" <?= ($filters['status'] ?? '') === 'reserved' ? 'selected' : '' ?>>Reserved</option>
                                <option value="occupied" <?= ($filters['status'] ?? '') === 'occupied' ? 'selected' : '' ?>>Occupied</option>
                            </select>
                        </div>
                        
                        <!-- Category -->
                        <div class="form__group">
                            <label for="category" class="form__label">Category</label>
                            <select id="category" name="category" class="form__select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= ($filters['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Floor -->
                        <div class="form__group">
                            <label for="floor" class="form__label">Floor</label>
                            <select id="floor" name="floor" class="form__select">
                                <option value="">All Floors</option>
                                <?php foreach ($floors as $floor): ?>
                                    <option value="<?= $floor['id'] ?>" <?= ($filters['floor_id'] ?? '') == $floor['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($floor['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Rent Range -->
                        <div class="form__group">
                            <label class="form__label">Monthly Rent (<?= APP_CURRENCY_SYMBOL ?>)</label>
                            <div class="flex" style="gap: 0.5rem; align-items: center;">
                                <input 
                                    type="number" 
                                    name="min_rent" 
                                    class="form__input" 
                                    placeholder="Min"
                                    value="<?= htmlspecialchars($filters['min_rent'] ?? '') ?>"
                                    min="<?= $rentRange['min_rent'] ?>"
                                    max="<?= $rentRange['max_rent'] ?>"
                                >
                                <span>to</span>
                                <input 
                                    type="number" 
                                    name="max_rent" 
                                    class="form__input" 
                                    placeholder="Max"
                                    value="<?= htmlspecialchars($filters['max_rent'] ?? '') ?>"
                                    min="<?= $rentRange['min_rent'] ?>"
                                    max="<?= $rentRange['max_rent'] ?>"
                                >
                            </div>
                            <div class="form__help">
                                Range: <?= APP_CURRENCY_SYMBOL ?><?= number_format($rentRange['min_rent']) ?> - <?= APP_CURRENCY_SYMBOL ?><?= number_format($rentRange['max_rent']) ?>
                            </div>
                        </div>
                        
                        <!-- Size Range -->
                        <div class="form__group">
                            <label class="form__label">Size (m²)</label>
                            <div class="flex" style="gap: 0.5rem; align-items: center;">
                                <input 
                                    type="number" 
                                    name="min_size" 
                                    class="form__input" 
                                    placeholder="Min"
                                    value="<?= htmlspecialchars($filters['min_size'] ?? '') ?>"
                                    min="<?= $sizeRange['min_size'] ?>"
                                    max="<?= $sizeRange['max_size'] ?>"
                                    step="0.1"
                                >
                                <span>to</span>
                                <input 
                                    type="number" 
                                    name="max_size" 
                                    class="form__input" 
                                    placeholder="Max"
                                    value="<?= htmlspecialchars($filters['max_size'] ?? '') ?>"
                                    min="<?= $sizeRange['min_size'] ?>"
                                    max="<?= $sizeRange['max_size'] ?>"
                                    step="0.1"
                                >
                            </div>
                            <div class="form__help">
                                Range: <?= $sizeRange['min_size'] ?>m² - <?= $sizeRange['max_size'] ?>m²
                            </div>
                        </div>
                        
                        <!-- Amenities -->
                        <div class="form__group">
                            <label class="form__label">Amenities</label>
                            <div class="amenities-list">
                                <?php foreach ($amenities as $amenity): ?>
                                    <label class="flex" style="align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <input 
                                            type="checkbox" 
                                            name="amenities[]" 
                                            value="<?= $amenity['id'] ?>"
                                            <?= in_array($amenity['id'], $filters['amenities'] ?? []) ? 'checked' : '' ?>
                                        >
                                        <span class="text-sm"><?= htmlspecialchars($amenity['name']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form__group">
                            <button type="submit" class="btn btn--primary" style="width: 100%;">
                                Apply Filters
                            </button>
                            <a href="/shops" class="btn btn--secondary mt-2" style="width: 100%;">
                                Clear All
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Shop Grid -->
        <main class="shops-grid">
            <?php if (empty($shops)): ?>
                <div class="card">
                    <div class="card__body text-center">
                        <h3>No shops found</h3>
                        <p class="text-muted">Try adjusting your filters or search terms.</p>
                        <a href="/shops" class="btn btn--primary mt-3">View All Shops</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid--cols-3">
                    <?php foreach ($shops as $shop): ?>
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
                                    <?php
                                    $statusClass = [
                                        'available' => 'badge--success',
                                        'reserved' => 'badge--warning',
                                        'occupied' => 'badge--error'
                                    ][$shop['status']] ?? 'badge--secondary';
                                    ?>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= ucfirst($shop['status']) ?>
                                    </span>
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
                                    <?php if ($shop['deposit_amount'] > 0): ?>
                                        <p class="text-sm text-muted">
                                            Deposit: <?= APP_CURRENCY_SYMBOL ?><?= number_format($shop['deposit_amount']) ?>
                                        </p>
                                    <?php endif; ?>
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
                
                <!-- Pagination -->
                <?php if ($pagination['total'] > 1): ?>
                    <div class="pagination-wrapper mt-5">
                        <nav class="pagination">
                            <?php
                            $currentPage = $pagination['current'];
                            $totalPages = $pagination['total'];
                            $queryParams = $_GET;
                            ?>
                            
                            <!-- Previous -->
                            <?php if ($currentPage > 1): ?>
                                <?php
                                $queryParams['page'] = $currentPage - 1;
                                $prevUrl = '/shops?' . http_build_query($queryParams);
                                ?>
                                <a href="<?= $prevUrl ?>" class="btn btn--secondary">Previous</a>
                            <?php endif; ?>
                            
                            <!-- Page Numbers -->
                            <div class="pagination__numbers">
                                <?php
                                $start = max(1, $currentPage - 2);
                                $end = min($totalPages, $currentPage + 2);
                                
                                for ($i = $start; $i <= $end; $i++):
                                    $queryParams['page'] = $i;
                                    $pageUrl = '/shops?' . http_build_query($queryParams);
                                ?>
                                    <a href="<?= $pageUrl ?>" 
                                       class="btn <?= $i === $currentPage ? 'btn--primary' : 'btn--secondary' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>
                            
                            <!-- Next -->
                            <?php if ($currentPage < $totalPages): ?>
                                <?php
                                $queryParams['page'] = $currentPage + 1;
                                $nextUrl = '/shops?' . http_build_query($queryParams);
                                ?>
                                <a href="<?= $nextUrl ?>" class="btn btn--secondary">Next</a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- Styles -->
<style>
.shops-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
    margin-top: 1rem;
}

.filters-sidebar {
    position: sticky;
    top: 1rem;
    height: fit-content;
}

.shops-grid {
    min-width: 0; /* Prevent grid overflow */
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

.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.pagination__numbers {
    display: flex;
    gap: 0.25rem;
}

#toggle-filters {
    display: none;
}

@media (max-width: 768px) {
    .shops-layout {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .filters-sidebar {
        position: static;
        display: none;
    }
    
    .filters-sidebar.show {
        display: block;
    }
    
    #toggle-filters {
        display: inline-flex;
    }
    
    .shops-grid .grid--cols-3 {
        grid-template-columns: 1fr;
    }
    
    .pagination {
        flex-wrap: wrap;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .shops-layout {
        grid-template-columns: 250px 1fr;
    }
    
    .shops-grid .grid--cols-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle filters on mobile
    const toggleBtn = document.getElementById('toggle-filters');
    const sidebar = document.getElementById('filters-sidebar');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
    
    // Auto-submit form on filter change
    const form = document.getElementById('filters-form');
    const inputs = form.querySelectorAll('select, input[type="checkbox"]');
    
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            // Small delay to allow multiple quick changes
            clearTimeout(this.submitTimeout);
            this.submitTimeout = setTimeout(() => {
                form.submit();
            }, 300);
        });
    });
    
    // Handle number inputs with debounce
    const numberInputs = form.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(this.submitTimeout);
            this.submitTimeout = setTimeout(() => {
                form.submit();
            }, 1000);
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/Views/layout.php';
?>
