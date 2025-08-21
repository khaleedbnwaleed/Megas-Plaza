<?php
$title = '404 - Page Not Found';
ob_start();
?>

<div class="text-center" style="padding: 4rem 0;">
    <h1 style="font-size: 6rem; color: var(--gray-300); margin-bottom: 1rem;">404</h1>
    <h2 class="mb-3">Page Not Found</h2>
    <p class="text-muted mb-4">The page you are looking for doesn't exist or has been moved.</p>
    <a href="/" class="btn btn--primary">Go Home</a>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/Views/layout.php';
?>
