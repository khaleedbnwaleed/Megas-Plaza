<?php

require_once 'app/Models/Application.php';
require_once 'app/Models/Shop.php';
require_once 'app/Models/Lease.php';
require_once 'app/Services/Audit.php';

class ApplicationController {
    private $applicationModel;
    private $shopModel;
    private $leaseModel;
    private $audit;
    
    public function __construct() {
        $this->applicationModel = new Application();
        $this->shopModel = new Shop();
        $this->leaseModel = new Lease();
        $this->audit = new Audit();
    }
    
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            Response::redirect('/login');
        }
        
        $shopId = Request::get('shop_id');
        if (!$shopId) {
            Response::redirect('/shops');
        }
        
        $shop = $this->shopModel->findById($shopId);
        if (!$shop || $shop['status'] !== 'available') {
            $_SESSION['error'] = 'Shop is not available for application.';
            Response::redirect('/shops');
        }
        
        // Check if user already has an active application for this shop
        if ($this->applicationModel->hasActiveApplication($_SESSION['user_id'], $shopId)) {
            $_SESSION['error'] = 'You already have an active application for this shop.';
            Response::redirect('/shops/' . $shopId);
        }
        
        if (Request::isPost()) {
            $data = [
                'user_id' => $_SESSION['user_id'],
                'shop_id' => $shopId,
                'business_name' => Request::post('business_name'),
                'business_type' => Request::post('business_type'),
                'business_description' => Request::post('business_description'),
                'contact_phone' => Request::post('contact_phone'),
                'contact_email' => Request::post('contact_email'),
                'preferred_start_date' => Request::post('preferred_start_date'),
                'documents' => []
            ];
            
            // Validate required fields
            $errors = [];
            if (empty($data['business_name'])) $errors[] = 'Business name is required';
            if (empty($data['business_type'])) $errors[] = 'Business type is required';
            if (empty($data['business_description'])) $errors[] = 'Business description is required';
            if (empty($data['contact_phone'])) $errors[] = 'Contact phone is required';
            if (empty($data['contact_email'])) $errors[] = 'Contact email is required';
            if (empty($data['preferred_start_date'])) $errors[] = 'Preferred start date is required';
            
            // Handle file uploads
            if (!empty($_FILES['documents']['name'][0])) {
                $uploadDir = 'storage/uploads/applications/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                foreach ($_FILES['documents']['name'] as $key => $filename) {
                    if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                        
                        if (in_array($fileExtension, $allowedExtensions)) {
                            $newFilename = uniqid() . '.' . $fileExtension;
                            $uploadPath = $uploadDir . $newFilename;
                            
                            if (move_uploaded_file($_FILES['documents']['tmp_name'][$key], $uploadPath)) {
                                $data['documents'][] = [
                                    'original_name' => $filename,
                                    'stored_name' => $newFilename,
                                    'path' => $uploadPath
                                ];
                            }
                        }
                    }
                }
            }
            
            if (empty($errors)) {
                if ($this->applicationModel->create($data)) {
                    $this->audit->log($_SESSION['user_id'], 'application_created', 'Application created for shop: ' . $shop['name']);
                    $_SESSION['success'] = 'Application submitted successfully!';
                    Response::redirect('/applications');
                } else {
                    $errors[] = 'Failed to submit application. Please try again.';
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
            }
        }
        
        require_once 'app/Views/applications/create.php';
    }
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            Response::redirect('/login');
        }
        
        $page = max(1, (int)Request::get('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $applications = $this->applicationModel->findByUser($_SESSION['user_id'], $limit, $offset);
        
        require_once 'app/Views/applications/index.php';
    }
    
    public function show() {
        if (!isset($_SESSION['user_id'])) {
            Response::redirect('/login');
        }
        
        $id = Request::get('id');
        $application = $this->applicationModel->findById($id);
        
        if (!$application || $application['user_id'] != $_SESSION['user_id']) {
            Response::notFound();
        }
        
        require_once 'app/Views/applications/show.php';
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
        
        $applications = $this->applicationModel->findAll($filters, $limit, $offset);
        $shops = $this->shopModel->findAll();
        $statusCounts = $this->applicationModel->getStatusCounts();
        
        require_once 'app/Views/applications/manage.php';
    }
    
    public function updateStatus() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        if (Request::isPost()) {
            $id = Request::post('application_id');
            $status = Request::post('status');
            $notes = Request::post('admin_notes');
            
            $application = $this->applicationModel->findById($id);
            if (!$application) {
                Response::notFound();
            }
            
            if ($this->applicationModel->updateStatus($id, $status, $notes)) {
                // If approved, create lease
                if ($status === 'approved') {
                    $leaseData = [
                        'application_id' => $id,
                        'user_id' => $application['user_id'],
                        'shop_id' => $application['shop_id'],
                        'start_date' => $application['preferred_start_date'],
                        'end_date' => date('Y-m-d', strtotime($application['preferred_start_date'] . ' +1 year')),
                        'rent_amount' => $application['rent_amount'],
                        'security_deposit' => $application['rent_amount'] * 2,
                        'terms' => 'Standard lease terms and conditions apply.'
                    ];
                    
                    $this->leaseModel->create($leaseData);
                    $this->shopModel->updateStatus($application['shop_id'], 'occupied');
                }
                
                $this->audit->log($_SESSION['user_id'], 'application_status_updated', 
                    "Application #{$id} status changed to {$status}");
                
                $_SESSION['success'] = 'Application status updated successfully!';
            } else {
                $_SESSION['error'] = 'Failed to update application status.';
            }
        }
        
        Response::redirect('/applications/manage');
    }
}
