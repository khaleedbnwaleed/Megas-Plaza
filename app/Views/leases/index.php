<?php $title = 'My Leases'; ?>
<?php include 'app/Views/layout.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Leases</h1>
            <a href="/applications" class="text-blue-600 hover:text-blue-800">View Applications</a>
        </div>

        <?php if (empty($leases)): ?>
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Leases</h3>
                <p class="text-gray-500 mb-4">You don't have any active lease agreements yet.</p>
                <a href="/applications" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    View My Applications
                </a>
            </div>
        <?php else: ?>
            <!-- Leases List -->
            <div class="space-y-6">
                <?php foreach ($leases as $lease): ?>
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($lease['shop_name']) ?></h3>
                                <p class="text-gray-600"><?= htmlspecialchars($lease['location']) ?></p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($lease['business_name']) ?> (<?= ucfirst($lease['business_type']) ?>)</p>
                            </div>
                            <div class="text-right">
                                <?php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'expired' => 'bg-red-100 text-red-800',
                                    'terminated' => 'bg-gray-100 text-gray-800'
                                ];
                                $statusColor = $statusColors[$lease['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?= $statusColor ?>">
                                    <?= ucfirst($lease['status']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Start Date</span>
                                <p class="text-gray-900"><?= date('M j, Y', strtotime($lease['start_date'])) ?></p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">End Date</span>
                                <p class="text-gray-900"><?= date('M j, Y', strtotime($lease['end_date'])) ?></p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Monthly Rent</span>
                                <p class="text-gray-900">$<?= number_format($lease['rent_amount'], 2) ?></p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Security Deposit</span>
                                <p class="text-gray-900">$<?= number_format($lease['security_deposit'], 2) ?></p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                Lease created on <?= date('M j, Y', strtotime($lease['created_at'])) ?>
                            </div>
                            <a href="/leases/<?= $lease['id'] ?>" 
                               class="text-blue-600 hover:text-blue-800 font-medium">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
