<?php
/**
 * Response Helper
 * 
 * Handles HTTP responses and redirects
 */

class Response {
    
    public static function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    public static function redirect($url, $status = 302) {
        http_response_code($status);
        header('Location: ' . $url);
        exit;
    }
    
    public static function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
        self::redirect($referer);
    }
    
    public static function view($template, $data = []) {
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = APP_PATH . '/Views/' . $template . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("View file not found: {$template}");
        }
        
        // Get the content and clean the buffer
        $content = ob_get_clean();
        echo $content;
    }
    
    public static function error($code, $message = null) {
        http_response_code($code);
        
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error'
        ];
        
        $message = $message ?? $messages[$code] ?? 'Error';
        
        self::view("errors/{$code}", ['message' => $message]);
        exit;
    }
    
    public static function download($file, $name = null) {
        if (!file_exists($file)) {
            self::error(404, 'File not found');
        }
        
        $name = $name ?? basename($file);
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Length: ' . filesize($file));
        
        readfile($file);
        exit;
    }
    
    public static function setFlash($type, $message) {
        $_SESSION['flash'][$type] = $message;
    }
    
    public static function getFlash($type = null) {
        if ($type === null) {
            $flash = $_SESSION['flash'] ?? [];
            unset($_SESSION['flash']);
            return $flash;
        }
        
        $message = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    
    public static function hasFlash($type = null) {
        if ($type === null) {
            return !empty($_SESSION['flash']);
        }
        return isset($_SESSION['flash'][$type]);
    }
}
