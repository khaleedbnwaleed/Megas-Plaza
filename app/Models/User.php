<?php
/**
 * User Model
 * 
 * Handles user data operations and authentication
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Find user by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND status = 'active'");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Create new user
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO users (role, full_name, email, phone, password_hash, email_verified, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['role'] ?? 'tenant',
            $data['full_name'],
            $data['email'],
            $data['phone'] ?? null,
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['email_verified'] ?? 0,
            $data['status'] ?? 'active'
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, ['full_name', 'email', 'phone', 'email_verified', 'status'])) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Update password
     */
    public function updatePassword($id, $password) {
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($user, $password) {
        return password_verify($password, $user['password_hash']);
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT id FROM users WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get all users with pagination
     */
    public function getAll($page = 1, $limit = 10, $role = null) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT id, role, full_name, email, phone, email_verified, status, created_at FROM users";
        $params = [];
        
        if ($role) {
            $sql .= " WHERE role = ?";
            $params[] = $role;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Count users
     */
    public function count($role = null) {
        $sql = "SELECT COUNT(*) FROM users";
        $params = [];
        
        if ($role) {
            $sql .= " WHERE role = ?";
            $params[] = $role;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    
    /**
     * Create session record
     */
    public function createSession($userId, $sessionId) {
        $stmt = $this->db->prepare("
            INSERT INTO user_sessions (user_id, session_id, ip, user_agent) 
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $userId,
            $sessionId,
            Request::ip(),
            Request::userAgent()
        ]);
    }
    
    /**
     * Delete session
     */
    public function deleteSession($sessionId) {
        $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE session_id = ?");
        return $stmt->execute([$sessionId]);
    }
    
    /**
     * Clean old sessions
     */
    public function cleanOldSessions() {
        $stmt = $this->db->prepare("
            DELETE FROM user_sessions 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL ? SECOND)
        ");
        return $stmt->execute([SESSION_LIFETIME]);
    }
    
    /**
     * Get user permissions based on role
     */
    public function getPermissions($role) {
        $permissions = [
            'super_admin' => [
                'manage_users', 'manage_shops', 'manage_applications', 'manage_leases',
                'manage_invoices', 'manage_payments', 'manage_tickets', 'view_reports',
                'manage_settings', 'view_audit_log'
            ],
            'manager' => [
                'manage_shops', 'manage_applications', 'manage_leases',
                'manage_invoices', 'manage_payments', 'manage_tickets', 'view_reports'
            ],
            'tenant' => [
                'view_shops', 'create_applications', 'view_leases', 'view_invoices',
                'create_tickets', 'update_profile'
            ]
        ];
        
        return $permissions[$role] ?? [];
    }
    
    /**
     * Check if user has permission
     */
    public function hasPermission($user, $permission) {
        $permissions = $this->getPermissions($user['role']);
        return in_array($permission, $permissions);
    }
}
