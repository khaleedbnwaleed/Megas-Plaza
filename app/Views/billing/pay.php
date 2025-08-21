<?php $title = 'Pay Invoice'; ?>
<?php include 'app/Views/layout.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pay Invoice</h1>
            <p class="text-gray-600">Invoice <?= htmlspecialchars($invoice['invoice_number']) ?></p>
        </div>

        <!-- Invoice Summary -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="font-semibold text-blue-900 mb-2">Invoice Details</h3>
                    <p class="text-blue-800"><strong>Shop:</strong> <?= htmlspecialchars($invoice['shop_name']) ?></p>
                    <p class="text-blue-800"><strong>Business:</strong> <?= htmlspecialchars($invoice['business_name']) ?></p>
                    <p class="text-blue-800"><strong>Due Date:</strong> <?= date('M j, Y', strtotime($invoice['due_date'])) ?></p>
                </div>
                <div>
                    <h3 class="font-semibold text-blue-900 mb-2">Payment Summary</h3>
                    <p class="text-blue-800"><strong>Invoice Amount:</strong> $<?= number_format($invoice['amount'], 2) ?></p>
                    <p class="text-blue-800"><strong>Already Paid:</strong> $<?= number_format($totalPaid, 2) ?></p>
                    <p class="text-blue-800 text-lg"><strong>Balance Due:</strong> $<?= number_format($balance, 2) ?></p>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Information</h2>
            
            <form method="POST" class="space-y-6">
                <!-- Payment Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Payment Amount *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" max="<?= $balance ?>"
                               value="<?= $balance ?>" required
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Maximum amount: $<?= number_format($balance, 2) ?></p>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="credit_card" checked
                                   class="mr-3 text-blue-600 focus:ring-blue-500">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <span>Credit Card</span>
                            </div>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="bank_transfer"
                                   class="mr-3 text-blue-600 focus:ring-blue-500">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                                <span>Bank Transfer</span>
                            </div>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="cash"
                                   class="mr-3 text-blue-600 focus:ring-blue-500">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Cash</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Payment Notice -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div class="text-sm text-yellow-800">
                            <p><strong>Demo Mode:</strong> This is a demonstration payment system. No actual payment will be processed.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-between items-center pt-6">
                    <a href="/billing/invoices/<?= $invoice['id'] ?>" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        Process Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
