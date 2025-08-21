<?php $title = 'Invoice ' . $invoice['invoice_number']; ?>
<?php include 'app/Views/layout.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Invoice Details</h1>
            <div class="flex space-x-4">
                <a href="/billing/invoices" class="text-blue-600 hover:text-blue-800">← Back to Invoices</a>
                <?php if ($balance > 0): ?>
                    <a href="/billing/pay?invoice_id=<?= $invoice['id'] ?>" 
                       class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        Pay Now
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Invoice Header -->
            <div class="bg-gray-50 px-6 py-4 border-b">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($invoice['invoice_number']) ?></h2>
                        <p class="text-gray-600 mt-1"><?= htmlspecialchars($invoice['description']) ?></p>
                    </div>
                    <div class="text-right">
                        <?php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'paid' => 'bg-green-100 text-green-800'
                        ];
                        $status = $invoice['status'];
                        if ($status === 'pending' && strtotime($invoice['due_date']) < time()) {
                            $status = 'overdue';
                            $statusColors['overdue'] = 'bg-red-100 text-red-800';
                        }
                        $statusColor = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full <?= $statusColor ?>">
                            <?= ucfirst($status) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Bill To -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bill To</h3>
                        <div class="text-gray-700">
                            <p class="font-medium"><?= htmlspecialchars($invoice['first_name'] . ' ' . $invoice['last_name']) ?></p>
                            <p><?= htmlspecialchars($invoice['business_name']) ?></p>
                            <p><?= htmlspecialchars($invoice['user_email']) ?></p>
                        </div>
                    </div>

                    <!-- Invoice Info -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Invoice Information</h3>
                        <div class="space-y-2 text-gray-700">
                            <div class="flex justify-between">
                                <span>Invoice Date:</span>
                                <span><?= date('M j, Y', strtotime($invoice['created_at'])) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Due Date:</span>
                                <span><?= date('M j, Y', strtotime($invoice['due_date'])) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Shop:</span>
                                <span><?= htmlspecialchars($invoice['shop_name']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Location:</span>
                                <span><?= htmlspecialchars($invoice['location']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amount Summary -->
                <div class="border-t pt-6">
                    <div class="max-w-md ml-auto">
                        <div class="space-y-2">
                            <div class="flex justify-between text-lg">
                                <span>Invoice Amount:</span>
                                <span class="font-semibold">$<?= number_format($invoice['amount'], 2) ?></span>
                            </div>
                            <div class="flex justify-between text-lg">
                                <span>Total Paid:</span>
                                <span class="font-semibold text-green-600">$<?= number_format($totalPaid, 2) ?></span>
                            </div>
                            <div class="border-t pt-2">
                                <div class="flex justify-between text-xl font-bold">
                                    <span>Balance Due:</span>
                                    <span class="<?= $balance > 0 ? 'text-red-600' : 'text-green-600' ?>">
                                        $<?= number_format($balance, 2) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <?php if (!empty($payments)): ?>
                    <div class="border-t pt-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment History</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Transaction ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                <?= date('M j, Y', strtotime($payment['payment_date'])) ?>
                                            </td>
                                            <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                                $<?= number_format($payment['amount'], 2) ?>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                <?= ucfirst($payment['payment_method']) ?>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-500">
                                                <?= htmlspecialchars($payment['transaction_id']) ?>
                                            </td>
                                            <td class="px-4 py-2 text-sm">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    <?= ucfirst($payment['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
