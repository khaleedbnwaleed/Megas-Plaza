<?php
/**
 * API Controller
 * 
 * Handles AJAX requests and API endpoints
 */

class ApiController {
    private $shopModel;
    
    public function __construct() {
        $this->shopModel = new Shop();
    }
    
    /**
     * Get shops for API
     */
    public function shops() {
        $filters = [
            'status' => Request::get('status'),
            'category_id' => Request::get('category'),
            'floor_id' => Request::get('floor'),
            'search' => Request::get('search')
        ];
        
        // Remove empty filters
        $filters = array_filter($filters);
        
        $page = max(1, (int) Request::get('page', 1));
        $limit = min(50, max(1, (int) Request::get('limit', 12)));
        
        $shops = $this->shopModel->getAll($filters, $page, $limit);
        $total = $this->shopModel->count($filters);
        
        Response::json([
            'shops' => $shops,
            'pagination' => [
                'current' => $page,
                'total' => ceil($total / $limit),
                'totalItems' => $total,
                'perPage' => $limit
            ]
        ]);
    }
    
    /**
     * Search shops
     */
    public function searchShops() {
        $query = Request::get('q', '');
        $limit = min(20, max(1, (int) Request::get('limit', 10)));
        
        if (strlen($query) < 2) {
            Response::json(['results' => []]);
            return;
        }
        
        $results = $this->shopModel->search($query, $limit);
        
        Response::json(['results' => $results]);
    }
    
    /**
     * Get dashboard stats (for admin)
     */
    public function dashboardStats() {
        Auth::requireAnyRole(['super_admin', 'manager']);
        
        $stats = $this->shopModel->getStats();
        
        // Add occupancy rate
        $stats['occupancy_rate'] = $stats['total_shops'] > 0 
            ? round(($stats['occupied_shops'] / $stats['total_shops']) * 100, 1)
            : 0;
        
        Response::json($stats);
    }
}
