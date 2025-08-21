<?php $title = 'Reports & Analytics'; ?>
<?php include 'app/Views/layout.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
            <a href="/admin/dashboard" class="text-blue-600 hover:text-blue-800">← Back to Dashboard</a>
        </div>

        <!-- Report Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                    <select id="type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="revenue" <?= Request::get('type') === 'revenue' ? 'selected' : '' ?>>Revenue Report</option>
                        <option value="occupancy" <?= Request::get('type') === 'occupancy' ? 'selected' : '' ?>>Occupancy Report</option>
                        <option value="applications" <?= Request::get('type') === 'applications' ? 'selected' : '' ?>>Applications Report</option>
                    </select>
                </div>
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                    <select id="period" name="period" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="week" <?= Request::get('period') === 'week' ? 'selected' : '' ?>>Last Week</option>
                        <option value="month" <?= Request::get('period') === 'month' ? 'selected' : '' ?>>Last Month</option>
                        <option value="year" <?= Request::get('period') === 'year' ? 'selected' : '' ?>>Last Year</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Report Content -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">
                <?= ucfirst(Request::get('type', 'revenue')) ?> Report - <?= ucfirst(Request::get('period', 'month')) ?>
            </h2>

            <?php if (Request::get('type', 'revenue') === 'revenue'): ?>
                <!-- Revenue Report -->
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-900">
                                $<?= number_format(array_sum(array_column($reportData, 'revenue')), 2) ?>
                            </div>
                            <div class="text-sm text-green-700">Total Revenue</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-900"><?= count($reportData) ?></div>
                            <div class="text-sm text-blue-700">Payment Days</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-purple-900">
                                $<?= count($reportData) > 0 ? number_format(array_sum(array_column($reportData, 'revenue')) / count($reportData), 2) : '0.00' ?>
                            </div>
                            <div class="text-sm text-purple-700">Daily Average</div>
                        </div>
                    </div>

                    <?php if (!empty($reportData)): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($reportData as $row): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= date('M j, Y', strtotime($row['date'])) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                $<?= number_format($row['revenue'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif (Request::get('type') === 'occupancy'): ?>
                <!-- Occupancy Report -->
                <div class="space-y-6">
                    <?php if (!empty($reportData)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($reportData as $row): ?>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-lg font-semibold text-gray-900 mb-2">
                                        <?= ucfirst($row['category']) ?>
                                    </div>
                                    <div class="text-3xl font-bold text-blue-600 mb-1">
                                        <?= $row['occupancy_rate'] ?>%
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <?= $row['occupied_shops'] ?>/<?= $row['total_shops'] ?> occupied
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif (Request::get('type') === 'applications'): ?>
                <!-- Applications Report -->
                <div class="space-y-6">
                    <?php if (!empty($reportData)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <?php foreach ($reportData as $status => $count): ?>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-2xl font-bold text-gray-900"><?= number_format($count) ?></div>
                                    <div class="text-sm text-gray-600"><?= ucfirst($status) ?> Applications</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($reportData)): ?>
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Data Available</h3>
                    <p class="text-gray-500">No data found for the selected report type and period.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
