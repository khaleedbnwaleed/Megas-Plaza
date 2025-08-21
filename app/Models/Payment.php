<?php

class Payment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO payments (invoice_id, user_id, amount, payment_method, 
                transaction_id, status, payment_date, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['invoice_id'],
            $data['user_id'],
            $data['amount'],
            $data['payment_method'],
            $data['transaction_id'],
            $data['status'],
            $data['payment_date']
        ]);
    }
    
    public function findById($id) {
        $sql = "SELECT p.*, i.invoice_number, i.amount as invoice_amount,
                s.name as shop_name, a.business_name
                FROM payments p 
                JOIN invoices i ON p.invoice_id = i.id
                JOIN leases l ON i.lease_id = l.id
                JOIN shops s ON l.shop_id = s.id 
                JOIN applications a ON l.application_id = a.id
                WHERE p.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByUser($userId, $limit = 10, $offset = 0) {
        $sql = "SELECT p.*, i.invoice_number, s.name as shop_name, a.business_name
                FROM payments p 
                JOIN invoices i ON p.invoice_id = i.id
                JOIN leases l ON i.lease_id = l.id
                JOIN shops s ON l.shop_id = s.id 
                JOIN applications a ON l.application_id = a.id
                WHERE p.user_id = ? 
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findByInvoice($invoiceId) {
        $sql = "SELECT * FROM payments WHERE invoice_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$invoiceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findAll($filters = [], $limit = 20, $offset = 0) {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = "p.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['payment_method'])) {
            $where[] = "p.payment_method = ?";
            $params[] = $filters['payment_method'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(i.invoice_number LIKE ? OR a.business_name LIKE ? OR p.transaction_id LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql = "SELECT p.*, i.invoice_number, s.name as shop_name,
                u.first_name, u.last_name, a.business_name
                FROM payments p 
                JOIN invoices i ON p.invoice_id = i.id
                JOIN leases l ON i.lease_id = l.id
                JOIN shops s ON l.shop_id = s.id 
                JOIN users u ON p.user_id = u.id 
                JOIN applications a ON l.application_id = a.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id, $status) {
        $sql = "UPDATE payments SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
    
    public function getTotalPaid($invoiceId) {
        $sql = "SELECT SUM(amount) FROM payments WHERE invoice_id = ? AND status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$invoiceId]);
        return $stmt->fetchColumn() ?: 0;
    }
    
    public function getRecentPayments($limit = 10) {
        $sql = "SELECT p.*, i.invoice_number, s.name as shop_name, 
                u.first_name, u.last_name, a.business_name
                FROM payments p 
                JOIN invoices i ON p.invoice_id = i.id
                JOIN leases l ON i.lease_id = l.id
                JOIN shops s ON l.shop_id = s.id 
                JOIN users u ON p.user_id = u.id 
                JOIN applications a ON l.application_id = a.id
                WHERE p.status = 'completed'
                ORDER BY p.payment_date DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
