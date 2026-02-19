<?php $__env->startSection('title', 'Database Tables'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-database mr-2"></i>
                    Database Tables
                </h2>
            </div>
        </div>
        
        <div class="p-6">
            <?php if(count($tableNames) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php $__currentLoopData = $tableNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tableName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('admin.table', $tableName)); ?>" class="block p-6 bg-white border border-gray-200 rounded-lg hover:shadow-lg hover:border-blue-500 transition-all duration-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-table text-blue-600 text-xl"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e($tableName); ?></h3>
                                    <p class="text-sm text-gray-500 mt-1">View and manage data</p>
                                </div>
                                <div class="ml-auto">
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No tables found in the database.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dzona/Downloads/crud/resources/views/admin/index.blade.php ENDPATH**/ ?>