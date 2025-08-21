<?php

class Application {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO applications (user_id, shop_id, business_name, business_type, 
                business_description, contact_phone, contact_email, preferred_start_date, 
                documents, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['user_id'],
            $data['shop_id'],
            $data['business_name'],
            $data['business_type'],
            $data['business_description'],
            $data['contact_phone'],
            $data['contact_email'],
            $data['preferred_start_date'],
            json_encode($data['documents'] ?? [])
        ]);
    }
    
    public function findById($id) {
        $sql = "SELECT a.*, s.name as shop_name, s.location, s.size, s.rent_amount,
                u.first_name, u.last_name, u.email as user_email
                FROM applications a 
                JOIN shops s ON a.shop_id = s.id 
                JOIN users u ON a.user_id = u.id 
                WHERE a.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['documents']) {
            $result['documents'] = json_decode($result['documents'], true);
        }
        
        return $result;
    }
    
    public function findByUser($userId, $limit = 10, $offset = 0) {
        $sql = "SELECT a.*, s.name as shop_name, s.location, s.size, s.rent_amount
                FROM applications a 
                JOIN shops s ON a.shop_id = s.id 
                WHERE a.user_id = ? 
                ORDER BY a.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findAll($filters = [], $limit = 20, $offset = 0) {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = "a.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['shop_id'])) {
            $where[] = "a.shop_id = ?";
            $params[] = $filters['shop_id'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(a.business_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql = "SELECT a.*, s.name as shop_name, s.location, s.size, s.rent_amount,
                u.first_name, u.last_name, u.email as user_email
                FROM applications a 
                JOIN shops s ON a.shop_id = s.id 
                JOIN users u ON a.user_id = u.id 
                WHERE " . implode(' AND ', $where) . "
                ORDER BY a.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id, $status, $notes = null) {
        $sql = "UPDATE applications SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $notes, $id]);
    }
    
    public function hasActiveApplication($userId, $shopId) {
        $sql = "SELECT COUNT(*) FROM applications 
                WHERE user_id = ? AND shop_id = ? AND status IN ('pending', 'approved')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $shopId]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function getStatusCounts() {
        $sql = "SELECT status, COUNT(*) as count FROM applications GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
