<?php $title = 'Application Details'; ?>
<?php include 'app/Views/layout.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Application Details</h1>
            <a href="/applications" class="text-blue-600 hover:text-blue-800">← Back to Applications</a>
        </div>

        <!-- Status Banner -->
        <?php
        $statusColors = [
            'pending' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'approved' => 'bg-green-50 border-green-200 text-green-800',
            'rejected' => 'bg-red-50 border-red-200 text-red-800'
        ];
        $statusColor = $statusColors[$application['status']] ?? 'bg-gray-50 border-gray-200 text-gray-800';
        ?>
        <div class="<?= $statusColor ?> border rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold">Application Status: <?= ucfirst($application['status']) ?></h3>
                    <p class="text-sm mt-1">Submitted on <?= date('F j, Y \a\t g:i A', strtotime($application['created_at'])) ?></p>
                    <?php if ($application['admin_notes']): ?>
                        <p class="text-sm mt-2"><strong>Admin Notes:</strong> <?= htmlspecialchars($application['admin_notes']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Shop Information -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Shop Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Shop Name:</span>
                        <p class="text-gray-900"><?= htmlspecialchars($application['shop_name']) ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Location:</span>
                        <p class="text-gray-900"><?= htmlspecialchars($application['location']) ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Size:</span>
                        <p class="text-gray-900"><?= htmlspecialchars($application['size']) ?> sq ft</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Monthly Rent:</span>
                        <p class="text-gray-900">$<?= number_format($application['rent_amount'], 2) ?></p>
                    </div>
                </div>
            </div>

            <!-- Business Information -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Business Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Business Name:</span>
                        <p class="text-gray-900"><?= htmlspecialchars($application['business_name']) ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Business Type:</span>
                        <p class="text-gray-900"><?= ucfirst($application['business_type']) ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Description:</span>
                        <p class="text-gray-900"><?= htmlspecialchars($application['business_description']) ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Preferred Start Date:</span>
                        <p class="text-gray-900"><?= date('F j, Y', strtotime($application['preferred_start_date'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Phone:</span>
                        <p class="text-gray-900"><?= htmlspecialchars($application['contact_phone']) ?></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Email:</span>
                        <p class="text-gray-900"><?= htmlspecialchars($application['contact_email']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Uploaded Documents</h2>
                <?php if (!empty($application['documents'])): ?>
                    <div class="space-y-2">
                        <?php foreach ($application['documents'] as $doc): ?>
                            <div class="flex items-center p-2 bg-gray-50 rounded">
                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-sm text-gray-900"><?= htmlspecialchars($doc['original_name']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-sm">No documents uploaded</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Next Steps -->
        <?php if ($application['status'] === 'approved'): ?>
            <div class="mt-8 bg-green-50 border border-green-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-green-800 mb-2">Congratulations! Your Application is Approved</h3>
                <p class="text-green-700 mb-4">Your lease agreement has been generated. Please check your lease details and complete the payment process.</p>
                <a href="/leases" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                    View Lease Details
                </a>
            </div>
        <?php elseif ($application['status'] === 'pending'): ?>
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Application Under Review</h3>
                <p class="text-blue-700">Your application is currently being reviewed by our team. We'll notify you once a decision is made. This typically takes 3-5 business days.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
