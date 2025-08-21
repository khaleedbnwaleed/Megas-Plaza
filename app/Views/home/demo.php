<?php
$title = 'Demo Guide - ' . APP_NAME;
ob_start();
?>

<div class="demo-guide">
    <div class="text-center mb-5">
        <h1>Demo Guide</h1>
        <p class="text-lg text-muted">Follow these steps to explore the complete shop rental system</p>
    </div>

    <div class="demo-steps">
        <!-- Step 1 -->
        <div class="card mb-4">
            <div class="card__header">
                <h3 class="card__title">Step 1: Browse Available Shops</h3>
            </div>
            <div class="card__body">
                <p class="mb-3">Start by exploring our shop catalog to see what's available.</p>
                <ul class="mb-3">
                    <li>View all shops with detailed information</li>
                    <li>Use filters to find shops by category, size, rent range</li>
                    <li>Search for specific shops by name or code</li>
                    <li>Check amenities and floor locations</li>
                </ul>
                <a href="/shops" class="btn btn--primary">Browse Shops</a>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="card mb-4">
            <div class="card__header">
                <h3 class="card__title">Step 2: Register as a Tenant</h3>
            </div>
            <div class="card__body">
                <p class="mb-3">Create a tenant account to apply for shops.</p>
                <ul class="mb-3">
                    <li>Register with your business information</li>
                    <li>Email verification (simulated in demo mode)</li>
                    <li>Access tenant dashboard and features</li>
                </ul>
                <div class="flex" style="gap: 1rem;">
                    <a href="/register" class="btn btn--primary">Register New Account</a>
                    <a href="/login" class="btn btn--secondary">Login with Demo Account</a>
                </div>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="card mb-4">
            <div class="card__header">
                <h3 class="card__title">Step 3: Submit Shop Application</h3>
            </div>
            <div class="card__body">
                <p class="mb-3">Apply for available shops through the tenant portal.</p>
                <ul class="mb-3">
                    <li>Submit application with business details</li>
                    <li>Track application status</li>
                    <li>Receive notifications on updates</li>
                </ul>
                <p class="text-sm text-muted">
                    <strong>Demo Tenant:</strong> tenant1@example.com / password
                </p>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="card mb-4">
            <div class="card__header">
                <h3 class="card__title">Step 4: Manager Reviews Application</h3>
            </div>
            <div class="card__body">
                <p class="mb-3">Managers review and approve/reject applications.</p>
                <ul class="mb-3">
                    <li>Review applicant information</li>
                    <li>Approve or reject with comments</li>
                    <li>Auto-generate draft lease on approval</li>
                </ul>
                <p class="text-sm text-muted">
                    <strong>Demo Manager:</strong> manager1@megaplaza.com / password
                </p>
                <a href="/login" class="btn btn--primary">Login as Manager</a>
            </div>
        </div>

        <!-- Step 5 -->
        <div class="card mb-4">
            <div class="card__header">
                <h3 class="card__title">Step 5: Lease Management</h3>
            </div>
            <div class="card__body">
                <p class="mb-3">Manage the complete lease lifecycle.</p>
                <ul class="mb-3">
                    <li>Activate approved leases</li>
                    <li>Generate invoices automatically</li>
                    <li>Track payments and balances</li>
                    <li>Handle lease renewals and terminations</li>
                </ul>
            </div>
        </div>

        <!-- Step 6 -->
        <div class="card mb-4">
            <div class="card__header">
                <h3 class="card__title">Step 6: Billing & Payments</h3>
            </div>
            <div class="card__body">
                <p class="mb-3">Complete billing and payment management system.</p>
                <ul class="mb-3">
                    <li>Auto-generate monthly invoices</li>
                    <li>Record payments (cash, transfer, online)</li>
                    <li>Generate printable receipts</li>
                    <li>Track overdue payments and late fees</li>
                </ul>
            </div>
        </div>

        <!-- Step 7 -->
        <div class="card mb-4">
            <div class="card__header">
                <h3 class="card__title">Step 7: Maintenance Tickets</h3>
            </div>
            <div class="card__body">
                <p class="mb-3">Handle maintenance requests and issues.</p>
                <ul class="mb-3">
                    <li>Tenants submit maintenance tickets</li>
                    <li>Managers track and update status</li>
                    <li>Comment threads for communication</li>
                    <li>File attachments for documentation</li>
                </ul>
            </div>
        </div>

        <!-- Step 8 -->
        <div class="card mb-4">
            <div class="card__header">
                <h3 class="card__title">Step 8: Reports & Analytics</h3>
            </div>
            <div class="card__body">
                <p class="mb-3">Comprehensive reporting and analytics dashboard.</p>
                <ul class="mb-3">
                    <li>Occupancy rates and trends</li>
                    <li>Revenue reports and forecasting</li>
                    <li>Tenant payment history</li>
                    <li>Export data to CSV</li>
                </ul>
                <p class="text-sm text-muted">
                    <strong>Demo Super Admin:</strong> admin@megaplaza.com / password
                </p>
                <a href="/login" class="btn btn--primary">Login as Super Admin</a>
            </div>
        </div>
    </div>

    <!-- Demo Accounts Summary -->
    <div class="card mt-5">
        <div class="card__header">
            <h3 class="card__title">Demo Accounts</h3>
        </div>
        <div class="card__body">
            <div class="grid grid--cols-3">
                <div class="text-center">
                    <h4 class="text-primary">Super Admin</h4>
                    <p class="text-sm font-mono">admin@megaplaza.com</p>
                    <p class="text-sm font-mono text-muted">password</p>
                    <p class="text-sm">Full system access, reports, settings</p>
                </div>
                <div class="text-center">
                    <h4 class="text-warning">Manager</h4>
                    <p class="text-sm font-mono">manager1@megaplaza.com</p>
                    <p class="text-sm font-mono text-muted">password</p>
                    <p class="text-sm">Shop management, applications, leases</p>
                </div>
                <div class="text-center">
                    <h4 class="text-success">Tenant</h4>
                    <p class="text-sm font-mono">tenant1@example.com</p>
                    <p class="text-sm font-mono text-muted">password</p>
                    <p class="text-sm">Browse shops, apply, manage leases</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Overview -->
    <div class="card mt-4">
        <div class="card__header">
            <h3 class="card__title">System Features</h3>
        </div>
        <div class="card__body">
            <div class="grid grid--cols-2">
                <div>
                    <h4>For Tenants</h4>
                    <ul class="text-sm">
                        <li>Browse and search shops</li>
                        <li>Submit rental applications</li>
                        <li>View lease agreements</li>
                        <li>Pay rent and view invoices</li>
                        <li>Submit maintenance tickets</li>
                        <li>Update profile information</li>
                    </ul>
                </div>
                <div>
                    <h4>For Managers/Admins</h4>
                    <ul class="text-sm">
                        <li>Manage shop inventory</li>
                        <li>Review and approve applications</li>
                        <li>Create and manage leases</li>
                        <li>Generate invoices and receipts</li>
                        <li>Track payments and balances</li>
                        <li>Handle maintenance requests</li>
                        <li>Generate reports and analytics</li>
                        <li>Audit logs and security</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.demo-guide {
    max-width: 800px;
    margin: 0 auto;
}

.demo-steps .card {
    border-left: 4px solid var(--primary-color);
}

.demo-steps ul {
    padding-left: 1.5rem;
}

.demo-steps li {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .demo-guide .grid--cols-3,
    .demo-guide .grid--cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
$content = ob_get_clean();
include APP_PATH . '/Views/layout.php';
?>
