<?php

require_once 'app/Models/User.php';
require_once 'app/Models/Shop.php';
require_once 'app/Models/Application.php';
require_once 'app/Models/Lease.php';
require_once 'app/Models/Invoice.php';
require_once 'app/Models/Payment.php';
require_once 'app/Services/Audit.php';

class AdminController {
    private $userModel;
    private $shopModel;
    private $applicationModel;
    private $leaseModel;
    private $invoiceModel;
    private $paymentModel;
    private $audit;
    private $db;
    
    public function __construct() {
        $this->userModel = new User();
        $this->shopModel = new Shop();
        $this->applicationModel = new Application();
        $this->leaseModel = new Lease();
        $this->invoiceModel = new Invoice();
        $this->paymentModel = new Payment();
        $this->audit = new Audit();
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function dashboard() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        $recentActivities = $this->getRecentActivities();
        $overdueInvoices = $this->invoiceModel->getOverdueInvoices();
        $expiringLeases = $this->leaseModel->getExpiringLeases(30);
        $recentPayments = $this->paymentModel->getRecentPayments(5);
        
        require_once 'app/Views/admin/dashboard.php';
    }
    
    public function users() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        $page = max(1, (int)Request::get('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'role' => Request::get('role'),
            'status' => Request::get('status'),
            'search' => Request::get('search')
        ];
        
        $users = $this->userModel->findAll($filters, $limit, $offset);
        $userStats = $this->getUserStats();
        
        require_once 'app/Views/admin/users.php';
    }
    
    public function shops() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        $page = max(1, (int)Request::get('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'status' => Request::get('status'),
            'category' => Request::get('category'),
            'search' => Request::get('search')
        ];
        
        $shops = $this->shopModel->findAll($filters, $limit, $offset);
        $shopStats = $this->getShopStats();
        
        require_once 'app/Views/admin/shops.php';
    }
    
    public function reports() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        $reportType = Request::get('type', 'revenue');
        $period = Request::get('period', 'month');
        
        $reportData = $this->generateReport($reportType, $period);
        
        require_once 'app/Views/admin/reports.php';
    }
    
    public function settings() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
            Response::forbidden();
        }
        
        if (Request::isPost()) {
            $settings = [
                'site_name' => Request::post('site_name'),
                'site_email' => Request::post('site_email'),
                'maintenance_mode' => Request::post('maintenance_mode') ? 1 : 0,
                'auto_invoice_generation' => Request::post('auto_invoice_generation') ? 1 : 0,
                'late_fee_percentage' => (float)Request::post('late_fee_percentage'),
                'grace_period_days' => (int)Request::post('grace_period_days')
            ];
            
            foreach ($settings as $key => $value) {
                $this->updateSetting($key, $value);
            }
            
            $this->audit->log($_SESSION['user_id'], 'settings_updated', 'System settings updated');
            $_SESSION['success'] = 'Settings updated successfully!';
        }
        
        $settings = $this->getSettings();
        require_once 'app/Views/admin/settings.php';
    }
    
    public function updateUserStatus() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        if (Request::isPost()) {
            $userId = Request::post('user_id');
            $status = Request::post('status');
            
            if ($this->userModel->updateStatus($userId, $status)) {
                $this->audit->log($_SESSION['user_id'], 'user_status_updated', 
                    "User #{$userId} status changed to {$status}");
                $_SESSION['success'] = 'User status updated successfully!';
            } else {
                $_SESSION['error'] = 'Failed to update user status.';
            }
        }
        
        Response::redirect('/admin/users');
    }
    
    public function createShop() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        if (Request::isPost()) {
            $data = [
                'name' => Request::post('name'),
                'description' => Request::post('description'),
                'location' => Request::post('location'),
                'size' => (int)Request::post('size'),
                'rent_amount' => (float)Request::post('rent_amount'),
                'category' => Request::post('category'),
                'amenities' => Request::post('amenities'),
                'status' => 'available'
            ];
            
            // Validate required fields
            $errors = [];
            if (empty($data['name'])) $errors[] = 'Shop name is required';
            if (empty($data['location'])) $errors[] = 'Location is required';
            if ($data['size'] <= 0) $errors[] = 'Valid size is required';
            if ($data['rent_amount'] <= 0) $errors[] = 'Valid rent amount is required';
            
            if (empty($errors)) {
                if ($this->shopModel->create($data)) {
                    $this->audit->log($_SESSION['user_id'], 'shop_created', 
                        "Shop '{$data['name']}' created");
                    $_SESSION['success'] = 'Shop created successfully!';
                    Response::redirect('/admin/shops');
                } else {
                    $errors[] = 'Failed to create shop. Please try again.';
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
            }
        }
        
        require_once 'app/Views/admin/create-shop.php';
    }
    
    private function getDashboardStats() {
        $stats = [];
        
        // User statistics
        $stats['total_users'] = $this->userModel->getTotalCount();
        $stats['new_users_today'] = $this->userModel->getNewUsersCount('today');
        $stats['active_tenants'] = $this->userModel->getActiveTenantsCount();
        
        // Shop statistics
        $stats['total_shops'] = $this->shopModel->getTotalCount();
        $stats['available_shops'] = $this->shopModel->getAvailableCount();
        $stats['occupied_shops'] = $this->shopModel->getOccupiedCount();
        $stats['occupancy_rate'] = $stats['total_shops'] > 0 ? 
            round(($stats['occupied_shops'] / $stats['total_shops']) * 100, 1) : 0;
        
        // Application statistics
        $stats['pending_applications'] = $this->applicationModel->getStatusCounts()['pending'] ?? 0;
        $stats['total_applications'] = array_sum($this->applicationModel->getStatusCounts());
        
        // Financial statistics
        $stats['monthly_revenue'] = $this->invoiceModel->getTotalRevenue('month');
        $stats['yearly_revenue'] = $this->invoiceModel->getTotalRevenue('year');
        $stats['overdue_amount'] = $this->getOverdueAmount();
        
        return $stats;
    }
    
    private function getRecentActivities() {
        $sql = "SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getUserStats() {
        $sql = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    private function getShopStats() {
        $sql = "SELECT status, COUNT(*) as count FROM shops GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    private function getOverdueAmount() {
        $sql = "SELECT SUM(amount) FROM invoices 
                WHERE status = 'pending' AND due_date < CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0;
    }
    
    private function generateReport($type, $period) {
        switch ($type) {
            case 'revenue':
                return $this->generateRevenueReport($period);
            case 'occupancy':
                return $this->generateOccupancyReport($period);
            case 'applications':
                return $this->generateApplicationsReport($period);
            default:
                return [];
        }
    }
    
    private function generateRevenueReport($period) {
        $dateCondition = match($period) {
            'week' => 'DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)',
            'month' => 'DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)',
            'year' => 'DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)',
            default => '1=1'
        };
        
        $sql = "SELECT DATE(created_at) as date, SUM(amount) as revenue
                FROM invoices 
                WHERE status = 'paid' AND {$dateCondition}
                GROUP BY DATE(created_at)
                ORDER BY date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function generateOccupancyReport($period) {
        $sql = "SELECT s.category, 
                COUNT(*) as total_shops,
                SUM(CASE WHEN s.status = 'occupied' THEN 1 ELSE 0 END) as occupied_shops,
                ROUND((SUM(CASE WHEN s.status = 'occupied' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as occupancy_rate
                FROM shops s
                GROUP BY s.category";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function generateApplicationsReport($period) {
        $dateCondition = match($period) {
            'week' => 'DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)',
            'month' => 'DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)',
            'year' => 'DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)',
            default => '1=1'
        };
        
        $sql = "SELECT status, COUNT(*) as count
                FROM applications 
                WHERE {$dateCondition}
                GROUP BY status";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    private function getSettings() {
        $sql = "SELECT setting_key, setting_value FROM settings";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    private function updateSetting($key, $value) {
        $sql = "INSERT INTO settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$key, $value]);
    }
}
