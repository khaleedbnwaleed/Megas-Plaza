<?php
$title = '500 - Server Error';
ob_start();
?>

<div class="text-center" style="padding: 4rem 0;">
    <h1 style="font-size: 6rem; color: var(--error-color); margin-bottom: 1rem;">500</h1>
    <h2 class="mb-3">Server Error</h2>
    <p class="text-muted mb-4">Something went wrong on our end. Please try again later.</p>
    <a href="/" class="btn btn--primary">Go Home</a>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/Views/layout.php';
?>
