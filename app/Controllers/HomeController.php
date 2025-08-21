<?php
/**
 * Home Controller
 * 
 * Handles homepage and public pages
 */

class HomeController {
    private $shopModel;
    
    public function __construct() {
        $this->shopModel = new Shop();
    }
    
    /**
     * Show homepage
     */
    public function index() {
        // Get featured shops
        $featuredShops = $this->shopModel->getFeatured(8);
        
        // Get statistics
        $stats = $this->shopModel->getStats();
        
        // Get categories for quick filters
        $categories = $this->shopModel->getCategories();
        
        Response::view('home/index', [
            'featuredShops' => $featuredShops,
            'stats' => $stats,
            'categories' => $categories
        ]);
    }
    
    /**
     * Show demo page
     */
    public function demo() {
        Response::view('home/demo');
    }
}
