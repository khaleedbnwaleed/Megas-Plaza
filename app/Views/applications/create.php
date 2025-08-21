<?php $title = 'Apply for Shop - ' . htmlspecialchars($shop['name']); ?>
<?php include 'app/Views/layout.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Apply for Shop</h1>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h2 class="text-xl font-semibold text-blue-900 mb-2"><?= htmlspecialchars($shop['name']) ?></h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-800">
                    <div><strong>Location:</strong> <?= htmlspecialchars($shop['location']) ?></div>
                    <div><strong>Size:</strong> <?= htmlspecialchars($shop['size']) ?> sq ft</div>
                    <div><strong>Rent:</strong> $<?= number_format($shop['rent_amount'], 2) ?>/month</div>
                </div>
            </div>
        </div>

        <!-- Application Form -->
        <form method="POST" enctype="multipart/form-data" class="bg-white shadow-lg rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Business Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h3>
                </div>
                
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                    <input type="text" id="business_name" name="business_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="<?= htmlspecialchars($_POST['business_name'] ?? '') ?>">
                </div>
                
                <div>
                    <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">Business Type *</label>
                    <select id="business_type" name="business_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Business Type</option>
                        <option value="retail" <?= ($_POST['business_type'] ?? '') === 'retail' ? 'selected' : '' ?>>Retail</option>
                        <option value="food" <?= ($_POST['business_type'] ?? '') === 'food' ? 'selected' : '' ?>>Food & Beverage</option>
                        <option value="services" <?= ($_POST['business_type'] ?? '') === 'services' ? 'selected' : '' ?>>Services</option>
                        <option value="electronics" <?= ($_POST['business_type'] ?? '') === 'electronics' ? 'selected' : '' ?>>Electronics</option>
                        <option value="fashion" <?= ($_POST['business_type'] ?? '') === 'fashion' ? 'selected' : '' ?>>Fashion</option>
                        <option value="other" <?= ($_POST['business_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label for="business_description" class="block text-sm font-medium text-gray-700 mb-2">Business Description *</label>
                    <textarea id="business_description" name="business_description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Describe your business, products/services, and target customers..."><?= htmlspecialchars($_POST['business_description'] ?? '') ?></textarea>
                </div>
                
                <!-- Contact Information -->
                <div class="md:col-span-2 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                </div>
                
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                    <input type="tel" id="contact_phone" name="contact_phone" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="<?= htmlspecialchars($_POST['contact_phone'] ?? '') ?>">
                </div>
                
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" id="contact_email" name="contact_email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="<?= htmlspecialchars($_POST['contact_email'] ?? '') ?>">
                </div>
                
                <!-- Lease Information -->
                <div class="md:col-span-2 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lease Information</h3>
                </div>
                
                <div>
                    <label for="preferred_start_date" class="block text-sm font-medium text-gray-700 mb-2">Preferred Start Date *</label>
                    <input type="date" id="preferred_start_date" name="preferred_start_date" required
                           min="<?= date('Y-m-d') ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="<?= htmlspecialchars($_POST['preferred_start_date'] ?? '') ?>">
                </div>
                
                <!-- Documents -->
                <div class="md:col-span-2 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Supporting Documents</h3>
                    <div>
                        <label for="documents" class="block text-sm font-medium text-gray-700 mb-2">Upload Documents</label>
                        <input type="file" id="documents" name="documents[]" multiple
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">
                            Upload business license, ID copy, financial statements, etc. (PDF, DOC, DOCX, JPG, PNG - Max 5MB each)
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Terms and Conditions -->
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-2">Application Terms</h4>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Security deposit of 2 months rent is required upon lease approval</li>
                    <li>• Minimum lease term is 12 months</li>
                    <li>• All applications are subject to approval and background verification</li>
                    <li>• Processing time is typically 3-5 business days</li>
                </ul>
                <label class="flex items-center mt-4">
                    <input type="checkbox" required class="mr-2">
                    <span class="text-sm text-gray-700">I agree to the terms and conditions</span>
                </label>
            </div>
            
            <!-- Submit Button -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="/shops/<?= $shop['id'] ?>" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</div>
