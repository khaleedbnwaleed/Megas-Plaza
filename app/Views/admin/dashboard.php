<?php $title = 'Admin Dashboard'; ?>
<?php include 'app/Views/layout.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-600 mt-2">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_users']) ?></p>
                        <p class="text-xs text-green-600">+<?= $stats['new_users_today'] ?> today</p>
                    </div>
                </div>
            </div>

            <!-- Shop Occupancy -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Occupancy Rate</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['occupancy_rate'] ?>%</p>
                        <p class="text-xs text-gray-500"><?= $stats['occupied_shops'] ?>/<?= $stats['total_shops'] ?> occupied</p>
                    </div>
                </div>
            </div>

            <!-- Pending Applications -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Applications</p>
                        <p class="text-2xl font-bold text-gray-900"><?= number_format($stats['pending_applications']) ?></p>
                        <p class="text-xs text-gray-500"><?= number_format($stats['total_applications']) ?> total</p>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Monthly Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">$<?= number_format($stats['monthly_revenue'], 2) ?></p>
                        <p class="text-xs text-gray-500">$<?= number_format($stats['yearly_revenue'], 2) ?> yearly</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Activities</h2>
                <div class="space-y-4">
                    <?php foreach (array_slice($recentActivities, 0, 5) as $activity): ?>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-2 w-2 bg-blue-500 rounded-full mt-2"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900"><?= htmlspecialchars($activity['description']) ?></p>
                                <p class="text-xs text-gray-500"><?= date('M j, Y g:i A', strtotime($activity['created_at'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4">
                    <a href="/admin/activities" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View all activities →</a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="/admin/shops/create" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span class="text-sm font-medium text-blue-900">Add Shop</span>
                    </a>
                    <a href="/applications/manage" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium text-green-900">Review Apps</span>
                    </a>
                    <a href="/billing/manage-invoices/generate-monthly" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-sm font-medium text-purple-900">Generate Invoices</span>
                    </a>
                    <a href="/admin/reports" class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                        <svg class="h-5 w-5 text-orange-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="text-sm font-medium text-orange-900">View Reports</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        <?php if (!empty($overdueInvoices) || !empty($expiringLeases)): ?>
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Overdue Invoices -->
                <?php if (!empty($overdueInvoices)): ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-red-900 mb-4">Overdue Invoices (<?= count($overdueInvoices) ?>)</h3>
                        <div class="space-y-3">
                            <?php foreach (array_slice($overdueInvoices, 0, 3) as $invoice): ?>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-red-900"><?= htmlspecialchars($invoice['business_name']) ?></p>
                                        <p class="text-xs text-red-700">$<?= number_format($invoice['amount'], 2) ?> • <?= $invoice['days_overdue'] ?> days overdue</p>
                                    </div>
                                    <a href="/billing/manage-invoices" class="text-red-600 hover:text-red-800 text-xs">View</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($overdueInvoices) > 3): ?>
                            <div class="mt-3 pt-3 border-t border-red-200">
                                <a href="/billing/manage-invoices?overdue=1" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    View all <?= count($overdueInvoices) ?> overdue invoices →
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Expiring Leases -->
                <?php if (!empty($expiringLeases)): ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-yellow-900 mb-4">Expiring Leases (<?= count($expiringLeases) ?>)</h3>
                        <div class="space-y-3">
                            <?php foreach (array_slice($expiringLeases, 0, 3) as $lease): ?>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-yellow-900"><?= htmlspecialchars($lease['shop_name']) ?></p>
                                        <p class="text-xs text-yellow-700"><?= htmlspecialchars($lease['first_name'] . ' ' . $lease['last_name']) ?> • Expires <?= date('M j, Y', strtotime($lease['end_date'])) ?></p>
                                    </div>
                                    <a href="/leases/manage" class="text-yellow-600 hover:text-yellow-800 text-xs">View</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($expiringLeases) > 3): ?>
                            <div class="mt-3 pt-3 border-t border-yellow-200">
                                <a href="/leases/manage" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                                    View all <?= count($expiringLeases) ?> expiring leases →
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Recent Payments -->
        <?php if (!empty($recentPayments)): ?>
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Payments</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tenant</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Shop</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($recentPayments as $payment): ?>
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900"><?= date('M j', strtotime($payment['payment_date'])) ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($payment['shop_name']) ?></td>
                                    <td class="px-4 py-2 text-sm font-medium text-gray-900">$<?= number_format($payment['amount'], 2) ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500"><?= ucfirst($payment['payment_method']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <a href="/billing/manage-payments" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View all payments →</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
