<?php
/**
 * Audit Service
 * 
 * Handles audit logging for security and compliance
 */

class Audit {
    
    /**
     * Log an action
     */
    public static function log($userId, $action, $details = null, $ip = null) {
        try {
            $db = getDB();
            
            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, details, ip, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userId,
                $action,
                $details,
                $ip ?? Request::ip()
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log('Audit log failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get audit logs with pagination
     */
    public static function getLogs($page = 1, $limit = 50, $filters = []) {
        $db = getDB();
        $offset = ($page - 1) * $limit;
        
        $sql = "
            SELECT a.*, u.full_name, u.email 
            FROM audit_logs a 
            LEFT JOIN users u ON a.user_id = u.id 
            WHERE 1=1
        ";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['user_id'])) {
            $sql .= " AND a.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['action'])) {
            $sql .= " AND a.action LIKE ?";
            $params[] = '%' . $filters['action'] . '%';
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(a.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(a.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['ip'])) {
            $sql .= " AND a.ip = ?";
            $params[] = $filters['ip'];
        }
        
        $sql .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Count audit logs
     */
    public static function countLogs($filters = []) {
        $db = getDB();
        
        $sql = "SELECT COUNT(*) FROM audit_logs WHERE 1=1";
        $params = [];
        
        // Apply same filters as getLogs
        if (!empty($filters['user_id'])) {
            $sql .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['action'])) {
            $sql .= " AND action LIKE ?";
            $params[] = '%' . $filters['action'] . '%';
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['ip'])) {
            $sql .= " AND ip = ?";
            $params[] = $filters['ip'];
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Clean old audit logs
     */
    public static function cleanOldLogs($days = 365) {
        try {
            $db = getDB();
            
            $stmt = $db->prepare("
                DELETE FROM audit_logs 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            
            $stmt->execute([$days]);
            
            return $stmt->rowCount();
            
        } catch (Exception $e) {
            error_log('Audit log cleanup failed: ' . $e->getMessage());
            return false;
        }
    }
}
