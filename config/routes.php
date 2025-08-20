<?php
/**
 * Application Routes
 * 
 * Maps URLs to Controller@method
 */

return [
    // Public Routes
    'GET /' => 'HomeController@index',
    'GET /shops' => 'ShopController@index',
    'GET /shops/{id}' => 'ShopController@show',
    'GET /register' => 'AuthController@showRegister',
    'POST /register' => 'AuthController@register',
    'GET /login' => 'AuthController@showLogin',
    'POST /login' => 'AuthController@login',
    'POST /logout' => 'AuthController@logout',
    'GET /demo' => 'HomeController@demo',
    
    // Auth Routes
    'GET /verify-email' => 'AuthController@verifyEmail',
    'GET /forgot-password' => 'AuthController@showForgotPassword',
    'POST /forgot-password' => 'AuthController@forgotPassword',
    'GET /reset-password' => 'AuthController@showResetPassword',
    'POST /reset-password' => 'AuthController@resetPassword',
    
    // Tenant Routes
    'GET /tenant/dashboard' => 'TenantController@dashboard',
    'GET /tenant/applications' => 'ApplicationController@tenantIndex',
    'POST /tenant/applications' => 'ApplicationController@store',
    'GET /tenant/applications/{id}' => 'ApplicationController@show',
    'POST /tenant/applications/{id}/cancel' => 'ApplicationController@cancel',
    'GET /tenant/leases' => 'LeaseController@tenantIndex',
    'GET /tenant/leases/{id}' => 'LeaseController@show',
    'POST /tenant/leases/{id}/accept' => 'LeaseController@accept',
    'GET /tenant/invoices' => 'InvoiceController@tenantIndex',
    'GET /tenant/invoices/{id}' => 'InvoiceController@show',
    'GET /tenant/payments' => 'PaymentController@tenantIndex',
    'GET /tenant/tickets' => 'TicketController@tenantIndex',
    'POST /tenant/tickets' => 'TicketController@store',
    'GET /tenant/tickets/{id}' => 'TicketController@show',
    'POST /tenant/tickets/{id}/comments' => 'TicketController@addComment',
    'GET /tenant/profile' => 'TenantController@profile',
    'POST /tenant/profile' => 'TenantController@updateProfile',
    
    // Admin Routes
    'GET /admin' => 'AdminController@dashboard',
    'GET /admin/shops' => 'AdminController@shops',
    'POST /admin/shops' => 'AdminController@createShop',
    'GET /admin/shops/{id}' => 'AdminController@showShop',
    'POST /admin/shops/{id}' => 'AdminController@updateShop',
    'DELETE /admin/shops/{id}' => 'AdminController@deleteShop',
    'GET /admin/applications' => 'ApplicationController@adminIndex',
    'POST /admin/applications/{id}/approve' => 'ApplicationController@approve',
    'POST /admin/applications/{id}/reject' => 'ApplicationController@reject',
    'GET /admin/leases' => 'LeaseController@adminIndex',
    'POST /admin/leases' => 'LeaseController@store',
    'POST /admin/leases/{id}/activate' => 'LeaseController@activate',
    'POST /admin/leases/{id}/terminate' => 'LeaseController@terminate',
    'GET /admin/invoices' => 'InvoiceController@adminIndex',
    'POST /admin/invoices/{id}/mark-paid' => 'InvoiceController@markPaid',
    'GET /admin/tenants' => 'AdminController@tenants',
    'GET /admin/tickets' => 'TicketController@adminIndex',
    'POST /admin/tickets/{id}/update-status' => 'TicketController@updateStatus',
    'GET /admin/reports' => 'ReportController@index',
    'GET /admin/reports/export' => 'ReportController@export',
    'GET /admin/settings' => 'AdminController@settings',
    'POST /admin/settings' => 'AdminController@updateSettings',
    'GET /admin/audit' => 'AdminController@auditLog',
    
    // API Routes
    'GET /api/shops' => 'ApiController@shops',
    'GET /api/dashboard-stats' => 'ApiController@dashboardStats',
    
    // Cron
    'GET /cron' => 'CronController@run',
];
