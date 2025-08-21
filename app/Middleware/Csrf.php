<?php
/**
 * CSRF Protection Middleware
 * 
 * Handles CSRF token generation and verification
 */

class Csrf {
    
    /**
     * Generate CSRF token
     */
    public static function token() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Generate CSRF token field for forms
     */
    public static function field() {
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . self::token() . '">';
    }
    
    /**
     * Verify CSRF token
     */
    public static function verify() {
        $token = Request::post(CSRF_TOKEN_NAME) ?? Request::input(CSRF_TOKEN_NAME);
        
        if (empty($token) || !isset($_SESSION['csrf_token'])) {
            self::fail();
        }
        
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            self::fail();
        }
        
        return true;
    }
    
    /**
     * Handle CSRF verification failure
     */
    private static function fail() {
        error_log('CSRF token verification failed from IP: ' . Request::ip());
        Response::error(419, 'CSRF token mismatch');
    }
    
    /**
     * Regenerate CSRF token
     */
    public static function regenerate() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
}
