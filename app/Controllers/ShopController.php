<?php
/**
 * Shop Controller
 * 
 * Handles shop catalog and details
 */

class ShopController {
    private $shopModel;
    
    public function __construct() {
        $this->shopModel = new Shop();
    }
    
    /**
     * Show shop catalog
     */
    public function index() {
        // Get filters from request
        $filters = [
            'status' => Request::get('status', 'available'),
            'category_id' => Request::get('category'),
            'floor_id' => Request::get('floor'),
            'min_size' => Request::get('min_size'),
            'max_size' => Request::get('max_size'),
            'min_rent' => Request::get('min_rent'),
            'max_rent' => Request::get('max_rent'),
            'search' => Request::get('search'),
            'amenities' => Request::get('amenities', [])
        ];
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null && $value !== [];
        });
        
        // Get pagination
        $page = max(1, (int) Request::get('page', 1));
        $limit = SHOPS_PER_PAGE;
        
        // Get shops
        $shops = $this->shopModel->getAll($filters, $page, $limit);
        $totalShops = $this->shopModel->count($filters);
        $totalPages = ceil($totalShops / $limit);
        
        // Get filter options
        $categories = $this->shopModel->getCategories();
        $floors = $this->shopModel->getFloors();
        $amenities = $this->shopModel->getAmenities();
        $rentRange = $this->shopModel->getRentRange();
        $sizeRange = $this->shopModel->getSizeRange();
        
        Response::view('shops/index', [
            'shops' => $shops,
            'filters' => $filters,
            'categories' => $categories,
            'floors' => $floors,
            'amenities' => $amenities,
            'rentRange' => $rentRange,
            'sizeRange' => $sizeRange,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'totalItems' => $totalShops,
                'perPage' => $limit
            ]
        ]);
    }
    
    /**
     * Show shop details
     */
    public function show($id) {
        $shop = $this->shopModel->find($id);
        
        if (!$shop) {
            Response::error(404, 'Shop not found');
        }
        
        // Get similar shops (same category, different shop)
        $similarShops = $this->shopModel->getAll([
            'category_id' => $shop['category_id'],
            'status' => 'available'
        ], 1, 4);
        
        // Remove current shop from similar shops
        $similarShops = array_filter($similarShops, function($s) use ($id) {
            return $s['id'] != $id;
        });
        
        Response::view('shops/show', [
            'shop' => $shop,
            'similarShops' => array_slice($similarShops, 0, 3)
        ]);
    }
}
