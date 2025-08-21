<?php
$title = '403 - Access Denied';
ob_start();
?>

<div class="text-center" style="padding: 4rem 0;">
    <h1 style="font-size: 6rem; color: var(--error-color); margin-bottom: 1rem;">403</h1>
    <h2 class="mb-3">Access Denied</h2>
    <p class="text-muted mb-4">You don't have permission to access this resource.</p>
    <a href="/" class="btn btn--primary">Go Home</a>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/Views/layout.php';
?>
