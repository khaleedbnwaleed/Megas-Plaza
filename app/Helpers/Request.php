<?php
/**
 * Request Helper
 * 
 * Handles HTTP request data and validation
 */

class Request {
    
    public static function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    public static function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    public static function input($key = null, $default = null) {
        $input = array_merge($_GET, $_POST);
        if ($key === null) {
            return $input;
        }
        return isset($input[$key]) ? $input[$key] : $default;
    }
    
    public static function file($key) {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }
    
    public static function method() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public static function isPost() {
        return self::method() === 'POST';
    }
    
    public static function isGet() {
        return self::method() === 'GET';
    }
    
    public static function ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    
    public static function userAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    public static function url() {
        return $_SERVER['REQUEST_URI'];
    }
    
    public static function fullUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    public static function validate($data, $rules) {
        return Validator::validate($data, $rules);
    }
}
