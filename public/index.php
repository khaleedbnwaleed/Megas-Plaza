<?php
/**
 * Mega School Plaza Entry Point
 * 
 * Main application bootstrap and router
 */

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

// Autoloader for classes
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/Controllers/',
        APP_PATH . '/Models/',
        APP_PATH . '/Middleware/',
        APP_PATH . '/Services/',
        APP_PATH . '/Helpers/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load routes
$routes = require_once __DIR__ . '/../config/routes.php';

// Simple Router
class Router {
    private $routes = [];
    
    public function __construct($routes) {
        $this->routes = $routes;
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path if running in subdirectory
        $basePath = str_replace('/public', '', dirname($_SERVER['SCRIPT_NAME']));
        if ($basePath !== '/') {
            $uri = str_replace($basePath, '', $uri);
        }
        
        $route = $method . ' ' . $uri;
        
        // Try exact match first
        if (isset($this->routes[$route])) {
            return $this->executeRoute($this->routes[$route], []);
        }
        
        // Try pattern matching for dynamic routes
        foreach ($this->routes as $pattern => $handler) {
            if ($this->matchRoute($pattern, $route, $params)) {
                return $this->executeRoute($handler, $params);
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        include APP_PATH . '/Views/errors/404.php';
    }
    
    private function matchRoute($pattern, $route, &$params) {
        $params = [];
        
        // Convert pattern to regex
        $regex = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        
        if (preg_match($regex, $route, $matches)) {
            array_shift($matches); // Remove full match
            
            // Extract parameter names
            preg_match_all('/\{([^}]+)\}/', $pattern, $paramNames);
            
            if (isset($paramNames[1])) {
                $params = array_combine($paramNames[1], $matches);
            }
            
            return true;
        }
        
        return false;
    }
    
    private function executeRoute($handler, $params) {
        list($controller, $method) = explode('@', $handler);
        
        if (!class_exists($controller)) {
            throw new Exception("Controller {$controller} not found");
        }
        
        $instance = new $controller();
        
        if (!method_exists($instance, $method)) {
            throw new Exception("Method {$method} not found in {$controller}");
        }
        
        return call_user_func_array([$instance, $method], $params);
    }
}

// Handle the request
try {
    $router = new Router($routes);
    $router->dispatch();
} catch (Exception $e) {
    if (APP_DEBUG) {
        echo "<h1>Error</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        error_log($e->getMessage());
        http_response_code(500);
        include APP_PATH . '/Views/errors/500.php';
    }
}
