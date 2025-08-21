<?php

require_once 'app/Models/Invoice.php';
require_once 'app/Models/Payment.php';
require_once 'app/Models/Lease.php';
require_once 'app/Services/Audit.php';

class BillingController {
    private $invoiceModel;
    private $paymentModel;
    private $leaseModel;
    private $audit;
    
    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->paymentModel = new Payment();
        $this->leaseModel = new Lease();
        $this->audit = new Audit();
    }
    
    public function invoices() {
        if (!isset($_SESSION['user_id'])) {
            Response::redirect('/login');
        }
        
        $page = max(1, (int)Request::get('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $invoices = $this->invoiceModel->findByUser($_SESSION['user_id'], $limit, $offset);
        
        require_once 'app/Views/billing/invoices.php';
    }
    
    public function invoice() {
        if (!isset($_SESSION['user_id'])) {
            Response::redirect('/login');
        }
        
        $id = Request::get('id');
        $invoice = $this->invoiceModel->findById($id);
        
        if (!$invoice || $invoice['user_id'] != $_SESSION['user_id']) {
            Response::notFound();
        }
        
        $payments = $this->paymentModel->findByInvoice($id);
        $totalPaid = $this->paymentModel->getTotalPaid($id);
        $balance = $invoice['amount'] - $totalPaid;
        
        require_once 'app/Views/billing/invoice.php';
    }
    
    public function pay() {
        if (!isset($_SESSION['user_id'])) {
            Response::redirect('/login');
        }
        
        $invoiceId = Request::get('invoice_id');
        $invoice = $this->invoiceModel->findById($invoiceId);
        
        if (!$invoice || $invoice['user_id'] != $_SESSION['user_id']) {
            Response::notFound();
        }
        
        $totalPaid = $this->paymentModel->getTotalPaid($invoiceId);
        $balance = $invoice['amount'] - $totalPaid;
        
        if ($balance <= 0) {
            $_SESSION['error'] = 'This invoice has already been paid in full.';
            Response::redirect('/billing/invoices/' . $invoiceId);
        }
        
        if (Request::isPost()) {
            $amount = (float)Request::post('amount');
            $paymentMethod = Request::post('payment_method');
            
            // Validate payment amount
            if ($amount <= 0 || $amount > $balance) {
                $_SESSION['error'] = 'Invalid payment amount.';
            } else {
                // Simulate payment processing
                $transactionId = 'TXN-' . time() . '-' . rand(1000, 9999);
                
                $paymentData = [
                    'invoice_id' => $invoiceId,
                    'user_id' => $_SESSION['user_id'],
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'transaction_id' => $transactionId,
                    'status' => 'completed', // In real app, this would be 'pending' initially
                    'payment_date' => date('Y-m-d H:i:s')
                ];
                
                if ($this->paymentModel->create($paymentData)) {
                    // Update invoice status if fully paid
                    $newTotalPaid = $totalPaid + $amount;
                    if ($newTotalPaid >= $invoice['amount']) {
                        $this->invoiceModel->updateStatus($invoiceId, 'paid');
                    }
                    
                    $this->audit->log($_SESSION['user_id'], 'payment_made', 
                        "Payment of ${amount} made for invoice #{$invoice['invoice_number']}");
                    
                    $_SESSION['success'] = 'Payment processed successfully!';
                    Response::redirect('/billing/invoices/' . $invoiceId);
                } else {
                    $_SESSION['error'] = 'Payment processing failed. Please try again.';
                }
            }
        }
        
        require_once 'app/Views/billing/pay.php';
    }
    
    public function payments() {
        if (!isset($_SESSION['user_id'])) {
            Response::redirect('/login');
        }
        
        $page = max(1, (int)Request::get('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $payments = $this->paymentModel->findByUser($_SESSION['user_id'], $limit, $offset);
        
        require_once 'app/Views/billing/payments.php';
    }
    
    public function manageInvoices() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        $page = max(1, (int)Request::get('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'status' => Request::get('status'),
            'overdue' => Request::get('overdue'),
            'search' => Request::get('search')
        ];
        
        $invoices = $this->invoiceModel->findAll($filters, $limit, $offset);
        $statusCounts = $this->invoiceModel->getStatusCounts();
        $overdueInvoices = $this->invoiceModel->getOverdueInvoices();
        
        require_once 'app/Views/billing/manage-invoices.php';
    }
    
    public function managePayments() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        $page = max(1, (int)Request::get('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'status' => Request::get('status'),
            'payment_method' => Request::get('payment_method'),
            'search' => Request::get('search')
        ];
        
        $payments = $this->paymentModel->findAll($filters, $limit, $offset);
        $recentPayments = $this->paymentModel->getRecentPayments(10);
        
        require_once 'app/Views/billing/manage-payments.php';
    }
    
    public function generateInvoice() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        if (Request::isPost()) {
            $leaseId = Request::post('lease_id');
            $amount = (float)Request::post('amount');
            $dueDate = Request::post('due_date');
            $description = Request::post('description');
            
            $lease = $this->leaseModel->findById($leaseId);
            if (!$lease) {
                $_SESSION['error'] = 'Lease not found.';
                Response::redirect('/billing/manage-invoices');
            }
            
            $invoiceData = [
                'lease_id' => $leaseId,
                'user_id' => $lease['user_id'],
                'invoice_number' => $this->invoiceModel->generateInvoiceNumber(),
                'amount' => $amount,
                'due_date' => $dueDate,
                'description' => $description
            ];
            
            if ($this->invoiceModel->create($invoiceData)) {
                $this->audit->log($_SESSION['user_id'], 'invoice_generated', 
                    "Invoice generated for lease #{$leaseId}");
                
                $_SESSION['success'] = 'Invoice generated successfully!';
            } else {
                $_SESSION['error'] = 'Failed to generate invoice.';
            }
        }
        
        Response::redirect('/billing/manage-invoices');
    }
    
    public function generateMonthlyInvoices() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['super_admin', 'manager'])) {
            Response::forbidden();
        }
        
        // Get all active leases
        $activeLeases = $this->leaseModel->findAll(['status' => 'active']);
        $generated = 0;
        
        foreach ($activeLeases as $lease) {
            // Check if invoice already exists for this month
            $currentMonth = date('Y-m');
            $existingInvoice = $this->db->prepare("
                SELECT COUNT(*) FROM invoices 
                WHERE lease_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?
            ");
            $existingInvoice->execute([$lease['id'], $currentMonth]);
            
            if ($existingInvoice->fetchColumn() == 0) {
                $invoiceData = [
                    'lease_id' => $lease['id'],
                    'user_id' => $lease['user_id'],
                    'invoice_number' => $this->invoiceModel->generateInvoiceNumber(),
                    'amount' => $lease['rent_amount'],
                    'due_date' => date('Y-m-d', strtotime('first day of next month')),
                    'description' => 'Monthly rent for ' . date('F Y')
                ];
                
                if ($this->invoiceModel->create($invoiceData)) {
                    $generated++;
                }
            }
        }
        
        $this->audit->log($_SESSION['user_id'], 'monthly_invoices_generated', 
            "Generated {$generated} monthly invoices");
        
        $_SESSION['success'] = "Generated {$generated} monthly invoices.";
        Response::redirect('/billing/manage-invoices');
    }
}
