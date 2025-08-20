<?php
/**
 * Mega School Plaza Configuration
 * 
 * Main configuration file for the application
 */

// Environment
define('APP_ENV', 'development'); // development, production
define('APP_DEBUG', true);
define('APP_NAME', 'Mega School Plaza');
define('APP_VERSION', '1.0.0');

// Base URL (adjust for your environment)
define('BASE_URL', 'http://localhost/megaplaza/public');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'megaplaza');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Timezone and Locale
date_default_timezone_set('Africa/Lagos');
define('APP_TIMEZONE', 'Africa/Lagos');
define('APP_CURRENCY', 'NGN');
define('APP_CURRENCY_SYMBOL', '₦');

// Session Configuration
define('SESSION_LIFETIME', 7200); // 2 hours
define('SESSION_NAME', 'megaplaza_session');

// Security
define('CSRF_TOKEN_NAME', '_token');
define('PASSWORD_MIN_LENGTH', 8);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File Upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);
define('UPLOAD_PATH', __DIR__ . '/../storage/uploads/');

// Email Configuration
define('MAIL_ENABLED', false); // Set to true when SMTP is configured
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', '');
define('MAIL_PASSWORD', '');
define('MAIL_FROM_EMAIL', 'noreply@megaplaza.com');
define('MAIL_FROM_NAME', 'Mega School Plaza');

// Pagination
define('ITEMS_PER_PAGE', 10);
define('SHOPS_PER_PAGE', 12);

// Business Settings
define('LATE_FEE_PERCENTAGE', 5.0);
define('GRACE_PERIOD_DAYS', 7);
define('INVOICE_PREFIX', 'INV');
define('RECEIPT_PREFIX', 'RCP');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('LOGS_PATH', STORAGE_PATH . '/logs');

// Create necessary directories
$dirs = [STORAGE_PATH, LOGS_PATH, UPLOAD_PATH];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Error Reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
