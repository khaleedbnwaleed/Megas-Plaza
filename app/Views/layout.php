<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Csrf::token() ?>">
    <title><?= $title ?? APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <?= $additionalCss ?? '' ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container header__container">
            <a href="/" class="header__logo"><?= APP_NAME ?></a>
            
            <nav class="header__nav">
                <?php if (Auth::check()): ?>
                    <?php $user = Auth::user(); ?>
                    <?php if (Auth::hasAnyRole(['super_admin', 'manager'])): ?>
                        <a href="/admin" class="header__nav-link">Admin</a>
                    <?php endif; ?>
                    
                    <?php if (Auth::hasRole('tenant')): ?>
                        <a href="/tenant/dashboard" class="header__nav-link">Dashboard</a>
                        <a href="/shops" class="header__nav-link">Browse Shops</a>
                    <?php endif; ?>
                    
                    <span class="header__nav-link">Hello, <?= htmlspecialchars($user['full_name']) ?></span>
                    
                    <form method="POST" action="/logout" style="display: inline;">
                        <?= Csrf::field() ?>
                        <button type="submit" class="btn btn--secondary btn--sm">Logout</button>
                    </form>
                <?php else: ?>
                    <a href="/shops" class="header__nav-link">Browse Shops</a>
                    <a href="/login" class="header__nav-link">Login</a>
                    <a href="/register" class="header__nav-link">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php 
    $flash = Response::getFlash();
    foreach ($flash as $type => $message): 
    ?>
        <div data-flash="<?= $type ?>" style="display: none;"><?= htmlspecialchars($message) ?></div>
    <?php endforeach; ?>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <?= $content ?? '' ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer__container">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
        </div>
    </footer>

    <script src="/assets/js/main.js"></script>
    <?= $additionalJs ?? '' ?>
</body>
</html>
