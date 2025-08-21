<?php
/**
 * Shop Model
 * 
 * Handles shop data operations and queries
 */

class Shop {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Get all shops with filters and pagination
     */
    public function getAll($filters = [], $page = 1, $limit = SHOPS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "
            SELECT s.*, c.name as category_name, f.name as floor_name,
                   (SELECT path FROM shop_images WHERE shop_id = s.id AND is_cover = 1 LIMIT 1) as cover_image
            FROM shops s
            LEFT JOIN categories c ON s.category_id = c.id
            LEFT JOIN floors f ON s.floor_id = f.id
            WHERE 1=1
        ";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['status'])) {
            $sql .= " AND s.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND s.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['floor_id'])) {
            $sql .= " AND s.floor_id = ?";
            $params[] = $filters['floor_id'];
        }
        
        if (!empty($filters['min_size'])) {
            $sql .= " AND s.size_sqm >= ?";
            $params[] = $filters['min_size'];
        }
        
        if (!empty($filters['max_size'])) {
            $sql .= " AND s.size_sqm <= ?";
            $params[] = $filters['max_size'];
        }
        
        if (!empty($filters['min_rent'])) {
            $sql .= " AND s.rent_monthly >= ?";
            $params[] = $filters['min_rent'];
        }
        
        if (!empty($filters['max_rent'])) {
            $sql .= " AND s.rent_monthly <= ?";
            $params[] = $filters['max_rent'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (s.name LIKE ? OR s.description LIKE ? OR s.code LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['amenities'])) {
            $amenityIds = is_array($filters['amenities']) ? $filters['amenities'] : [$filters['amenities']];
            $placeholders = str_repeat('?,', count($amenityIds) - 1) . '?';
            $sql .= " AND s.id IN (
                SELECT shop_id FROM shop_amenities 
                WHERE amenity_id IN ($placeholders)
                GROUP BY shop_id 
                HAVING COUNT(DISTINCT amenity_id) = ?
            )";
            $params = array_merge($params, $amenityIds, [count($amenityIds)]);
        }
        
        $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Count shops with filters
     */
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) FROM shops s WHERE 1=1";
        $params = [];
        
        // Apply same filters as getAll (without joins for performance)
        if (!empty($filters['status'])) {
            $sql .= " AND s.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND s.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['floor_id'])) {
            $sql .= " AND s.floor_id = ?";
            $params[] = $filters['floor_id'];
        }
        
        if (!empty($filters['min_size'])) {
            $sql .= " AND s.size_sqm >= ?";
            $params[] = $filters['min_size'];
        }
        
        if (!empty($filters['max_size'])) {
            $sql .= " AND s.size_sqm <= ?";
            $params[] = $filters['max_size'];
        }
        
        if (!empty($filters['min_rent'])) {
            $sql .= " AND s.rent_monthly >= ?";
            $params[] = $filters['min_rent'];
        }
        
        if (!empty($filters['max_rent'])) {
            $sql .= " AND s.rent_monthly <= ?";
            $params[] = $filters['max_rent'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (s.name LIKE ? OR s.description LIKE ? OR s.code LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['amenities'])) {
            $amenityIds = is_array($filters['amenities']) ? $filters['amenities'] : [$filters['amenities']];
            $placeholders = str_repeat('?,', count($amenityIds) - 1) . '?';
            $sql .= " AND s.id IN (
                SELECT shop_id FROM shop_amenities 
                WHERE amenity_id IN ($placeholders)
                GROUP BY shop_id 
                HAVING COUNT(DISTINCT amenity_id) = ?
            )";
            $params = array_merge($params, $amenityIds, [count($amenityIds)]);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Find shop by ID with full details
     */
    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as category_name, f.name as floor_name
            FROM shops s
            LEFT JOIN categories c ON s.category_id = c.id
            LEFT JOIN floors f ON s.floor_id = f.id
            WHERE s.id = ?
        ");
        
        $stmt->execute([$id]);
        $shop = $stmt->fetch();
        
        if ($shop) {
            // Get images
            $shop['images'] = $this->getShopImages($id);
            
            // Get amenities
            $shop['amenities'] = $this->getShopAmenities($id);
        }
        
        return $shop;
    }
    
    /**
     * Get shop images
     */
    public function getShopImages($shopId) {
        $stmt = $this->db->prepare("
            SELECT * FROM shop_images 
            WHERE shop_id = ? 
            ORDER BY is_cover DESC, id ASC
        ");
        
        $stmt->execute([$shopId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get shop amenities
     */
    public function getShopAmenities($shopId) {
        $stmt = $this->db->prepare("
            SELECT a.* FROM amenities a
            JOIN shop_amenities sa ON a.id = sa.amenity_id
            WHERE sa.shop_id = ?
            ORDER BY a.name
        ");
        
        $stmt->execute([$shopId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get featured shops (available shops for homepage)
     */
    public function getFeatured($limit = 8) {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as category_name, f.name as floor_name,
                   (SELECT path FROM shop_images WHERE shop_id = s.id AND is_cover = 1 LIMIT 1) as cover_image
            FROM shops s
            LEFT JOIN categories c ON s.category_id = c.id
            LEFT JOIN floors f ON s.floor_id = f.id
            WHERE s.status = 'available'
            ORDER BY s.created_at DESC
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all categories
     */
    public function getCategories() {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get all floors
     */
    public function getFloors() {
        $stmt = $this->db->prepare("SELECT * FROM floors ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get all amenities
     */
    public function getAmenities() {
        $stmt = $this->db->prepare("SELECT * FROM amenities ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get shop statistics
     */
    public function getStats() {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_shops,
                SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_shops,
                SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied_shops,
                SUM(CASE WHEN status = 'reserved' THEN 1 ELSE 0 END) as reserved_shops,
                AVG(rent_monthly) as avg_rent,
                MIN(rent_monthly) as min_rent,
                MAX(rent_monthly) as max_rent,
                AVG(size_sqm) as avg_size
            FROM shops
        ");
        
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Search shops (for API/AJAX)
     */
    public function search($query, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT s.id, s.code, s.name, s.rent_monthly, s.status, c.name as category_name,
                   (SELECT path FROM shop_images WHERE shop_id = s.id AND is_cover = 1 LIMIT 1) as cover_image
            FROM shops s
            LEFT JOIN categories c ON s.category_id = c.id
            WHERE s.name LIKE ? OR s.description LIKE ? OR s.code LIKE ?
            ORDER BY 
                CASE WHEN s.name LIKE ? THEN 1 ELSE 2 END,
                s.name
            LIMIT ?
        ");
        
        $searchTerm = '%' . $query . '%';
        $exactTerm = $query . '%';
        
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $exactTerm, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get rent range
     */
    public function getRentRange() {
        $stmt = $this->db->prepare("SELECT MIN(rent_monthly) as min_rent, MAX(rent_monthly) as max_rent FROM shops");
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get size range
     */
    public function getSizeRange() {
        $stmt = $this->db->prepare("SELECT MIN(size_sqm) as min_size, MAX(size_sqm) as max_size FROM shops");
        $stmt->execute();
        return $stmt->fetch();
    }
}
