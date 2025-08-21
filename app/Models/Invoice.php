<?php

class Invoice {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO invoices (lease_id, user_id, invoice_number, amount, due_date, 
                description, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['lease_id'],
            $data['user_id'],
            $data['invoice_number'],
            $data['amount'],
            $data['due_date'],
            $data['description']
        ]);
    }
    
    public function findById($id) {
        $sql = "SELECT i.*, l.rent_amount, l.start_date, l.end_date,
                s.name as shop_name, s.location,
                u.first_name, u.last_name, u.email as user_email,
                a.business_name
                FROM invoices i 
                JOIN leases l ON i.lease_id = l.id
                JOIN shops s ON l.shop_id = s.id 
                JOIN users u ON i.user_id = u.id 
                JOIN applications a ON l.application_id = a.id
                WHERE i.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByUser($userId, $limit = 10, $offset = 0) {
        $sql = "SELECT i.*, s.name as shop_name, s.location, a.business_name
                FROM invoices i 
                JOIN leases l ON i.lease_id = l.id
                JOIN shops s ON l.shop_id = s.id 
                JOIN applications a ON l.application_id = a.id
                WHERE i.user_id = ? 
                ORDER BY i.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findAll($filters = [], $limit = 20, $offset = 0) {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = "i.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['overdue'])) {
            $where[] = "i.status = 'pending' AND i.due_date < CURDATE()";
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(a.business_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR i.invoice_number LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql = "SELECT i.*, s.name as shop_name, s.location,
                u.first_name, u.last_name, u.email as user_email,
                a.business_name
                FROM invoices i 
                JOIN leases l ON i.lease_id = l.id
                JOIN shops s ON l.shop_id = s.id 
                JOIN users u ON i.user_id = u.id 
                JOIN applications a ON l.application_id = a.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY i.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id, $status) {
        $sql = "UPDATE invoices SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
    
    public function generateInvoiceNumber() {
        $sql = "SELECT COUNT(*) FROM invoices WHERE YEAR(created_at) = YEAR(NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $count = $stmt->fetchColumn() + 1;
        
        return 'INV-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
    
    public function getOverdueInvoices() {
        $sql = "SELECT i.*, s.name as shop_name, u.first_name, u.last_name, u.email,
                a.business_name, DATEDIFF(CURDATE(), i.due_date) as days_overdue
                FROM invoices i 
                JOIN leases l ON i.lease_id = l.id
                JOIN shops s ON l.shop_id = s.id 
                JOIN users u ON i.user_id = u.id 
                JOIN applications a ON l.application_id = a.id
                WHERE i.status = 'pending' AND i.due_date < CURDATE()
                ORDER BY i.due_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getStatusCounts() {
        $sql = "SELECT status, COUNT(*) as count FROM invoices GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    public function getTotalRevenue($period = 'month') {
        $dateCondition = match($period) {
            'week' => 'DATE(i.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)',
            'month' => 'DATE(i.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)',
            'year' => 'DATE(i.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)',
            default => '1=1'
        };
        
        $sql = "SELECT SUM(amount) FROM invoices WHERE status = 'paid' AND {$dateCondition}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0;
    }
}
