<?php

require_once 'app/Models/Lease.php';
require_once 'app/Services/Audit.php';

class LeaseController {
    private $leaseModel;
    private $audit;
    
    public function __construct() {
        $this->leaseModel = new Lease();
        $this->audit = new Audit();
    }
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            Response::redirect('/login');
        }
        
        $page = max(1, (int)Request::get('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $leases = $this->leaseModel->findByUser($_SESSION['user_id'], $limit, $offset);
        
        require_once 'app/Views/leases/index.php';
    }
    
    public function show() {
        if (!isset($_SESSION['user_id'])) {
            Response::redirect('/login');
        }
        
        $id = Request::get('id');
        $lease = $this->leaseModel->findById($id);
        
        if (!$lease || $lease['user_id'] != $_SESSION['user_id']) {
            Response::notFound();
        }
        
        require_once 'app/Views/leases/show.php';
    }
    
    public function manage() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        $page = max(1, (int)Request::get('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'status' => Request::get('status'),
            'shop_id' => Request::get('shop_id'),
            'search' => Request::get('search')
        ];
        
        $leases = $this->leaseModel->findAll($filters, $limit, $offset);
        $statusCounts = $this->leaseModel->getStatusCounts();
        $expiringLeases = $this->leaseModel->getExpiringLeases(30);
        
        require_once 'app/Views/leases/manage.php';
    }
    
    public function updateStatus() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        if (Request::isPost()) {
            $id = Request::post('lease_id');
            $status = Request::post('status');
            
            $lease = $this->leaseModel->findById($id);
            if (!$lease) {
                Response::notFound();
            }
            
            if ($this->leaseModel->updateStatus($id, $status)) {
                $this->audit->log($_SESSION['user_id'], 'lease_status_updated', 
                    "Lease #{$id} status changed to {$status}");
                
                $_SESSION['success'] = 'Lease status updated successfully!';
            } else {
                $_SESSION['error'] = 'Failed to update lease status.';
            }
        }
        
        Response::redirect('/leases/manage');
    }
}
