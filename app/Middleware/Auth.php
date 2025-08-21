<?php
/**
 * Authentication Middleware
 * 
 * Handles user authentication and session management
 */

class Auth {
    
    /**
     * Check if user is authenticated
     */
    public static function check() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get current authenticated user
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        
        static $user = null;
        
        if ($user === null) {
            $userModel = new User();
            $user = $userModel->find($_SESSION['user_id']);
        }
        
        return $user;
    }
    
    /**
     * Get user ID
     */
    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get user role
     */
    public static function role() {
        return $_SESSION['user_role'] ?? null;
    }
    
    /**
     * Check if user has specific role
     */
    public static function hasRole($role) {
        return self::role() === $role;
    }
    
    /**
     * Check if user has any of the specified roles
     */
    public static function hasAnyRole($roles) {
        return in_array(self::role(), (array) $roles);
    }
    
    /**
     * Check if user has permission
     */
    public static function can($permission) {
        $user = self::user();
        if (!$user) {
            return false;
        }
        
        $userModel = new User();
        return $userModel->hasPermission($user, $permission);
    }
    
    /**
     * Require authentication
     */
    public static function require() {
        if (!self::check()) {
            // Store intended URL
            $_SESSION['intended_url'] = Request::url();
            
            Response::setFlash('error', 'Please login to access this page');
            Response::redirect('/login');
        }
        
        // Check session timeout
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_LIFETIME) {
            self::logout();
            Response::setFlash('error', 'Your session has expired. Please login again.');
            Response::redirect('/login');
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Require specific role
     */
    public static function requireRole($role) {
        self::require();
        
        if (!self::hasRole($role)) {
            Response::error(403, 'Access denied');
        }
    }
    
    /**
     * Require any of the specified roles
     */
    public static function requireAnyRole($roles) {
        self::require();
        
        if (!self::hasAnyRole($roles)) {
            Response::error(403, 'Access denied');
        }
    }
    
    /**
     * Require permission
     */
    public static function requirePermission($permission) {
        self::require();
        
        if (!self::can($permission)) {
            Response::error(403, 'Access denied');
        }
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        if (self::check()) {
            $userModel = new User();
            $userModel->deleteSession(session_id());
        }
        
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
    
    /**
     * Check if user is guest (not authenticated)
     */
    public static function guest() {
        return !self::check();
    }
    
    /**
     * Redirect guests to login
     */
    public static function redirectGuests() {
        if (self::guest()) {
            Response::redirect('/login');
        }
    }
    
    /**
     * Redirect authenticated users
     */
    public static function redirectAuthenticated($url = '/') {
        if (self::check()) {
            Response::redirect($url);
        }
    }
}
