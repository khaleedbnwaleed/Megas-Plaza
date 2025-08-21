<?php

class Lease {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO leases (application_id, user_id, shop_id, start_date, end_date, 
                rent_amount, security_deposit, terms, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['application_id'],
            $data['user_id'],
            $data['shop_id'],
            $data['start_date'],
            $data['end_date'],
            $data['rent_amount'],
            $data['security_deposit'],
            $data['terms']
        ]);
    }
    
    public function findById($id) {
        $sql = "SELECT l.*, s.name as shop_name, s.location, s.size,
                u.first_name, u.last_name, u.email as user_email,
                a.business_name, a.business_type
                FROM leases l 
                JOIN shops s ON l.shop_id = s.id 
                JOIN users u ON l.user_id = u.id 
                JOIN applications a ON l.application_id = a.id
                WHERE l.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByUser($userId, $limit = 10, $offset = 0) {
        $sql = "SELECT l.*, s.name as shop_name, s.location, s.size,
                a.business_name, a.business_type
                FROM leases l 
                JOIN shops s ON l.shop_id = s.id 
                JOIN applications a ON l.application_id = a.id
                WHERE l.user_id = ? 
                ORDER BY l.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findAll($filters = [], $limit = 20, $offset = 0) {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = "l.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['shop_id'])) {
            $where[] = "l.shop_id = ?";
            $params[] = $filters['shop_id'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(a.business_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql = "SELECT l.*, s.name as shop_name, s.location, s.size,
                u.first_name, u.last_name, u.email as user_email,
                a.business_name, a.business_type
                FROM leases l 
                JOIN shops s ON l.shop_id = s.id 
                JOIN users u ON l.user_id = u.id 
                JOIN applications a ON l.application_id = a.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY l.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id, $status) {
        $sql = "UPDATE leases SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
    
    public function getExpiringLeases($days = 30) {
        $sql = "SELECT l.*, s.name as shop_name, u.first_name, u.last_name, u.email
                FROM leases l 
                JOIN shops s ON l.shop_id = s.id 
                JOIN users u ON l.user_id = u.id 
                WHERE l.status = 'active' 
                AND l.end_date <= DATE_ADD(NOW(), INTERVAL ? DAY)
                ORDER BY l.end_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getStatusCounts() {
        $sql = "SELECT status, COUNT(*) as count FROM leases GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
