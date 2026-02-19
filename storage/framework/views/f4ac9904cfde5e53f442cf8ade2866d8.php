<?php $__env->startSection('title', ucfirst($table)); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between flex-wrap">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-table mr-2"></i>
                        <?php echo e(ucfirst($table)); ?>

                    </h2>
                    <p class="text-sm text-gray-500 mt-1"><?php echo e($rows->total()); ?> total records</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="<?php echo e(route('admin.create', $table)); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Row
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Search and Filters -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" action="<?php echo e(route('admin.table', $table)); ?>" class="flex items-center gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input 
                            type="text" 
                            name="search" 
                            value="<?php echo e($search); ?>" 
                            placeholder="Search in table..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                <?php if($search): ?>
                    <a href="<?php echo e(route('admin.table', $table)); ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </form>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center space-x-2">
                                    <span><?php echo e(ucfirst(str_replace('_', ' ', $column['name']))); ?></span>
                                    <div class="flex flex-col">
                                        <a href="<?php echo e(route('admin.table', ['table' => $table, 'sort' => $column['name'], 'direction' => $sortColumn === $column['name'] && $sortDirection === 'asc' ? 'desc' : 'asc', 'search' => $search])); ?>" 
                                           class="text-gray-400 hover:text-gray-600 <?php echo e($sortColumn === $column['name'] && $sortDirection === 'asc' ? 'text-blue-600' : ''); ?>">
                                            <i class="fas fa-chevron-up text-xs"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.table', ['table' => $table, 'sort' => $column['name'], 'direction' => $sortColumn === $column['name'] && $sortDirection === 'desc' ? 'asc' : 'desc', 'search' => $search])); ?>" 
                                           class="text-gray-400 hover:text-gray-600 <?php echo e($sortColumn === $column['name'] && $sortDirection === 'desc' ? 'text-blue-600' : ''); ?>">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php
                                        $value = $row->{$column['name']};
                                        if ($value === null) {
                                            echo '<span class="text-gray-400 italic">NULL</span>';
                                        } elseif (is_bool($value)) {
                                            echo $value ? '<span class="text-green-600"><i class="fas fa-check"></i></span>' : '<span class="text-red-600"><i class="fas fa-times"></i></span>';
                                        } elseif (is_string($value) && strlen($value) > 50) {
                                            echo '<span title="' . htmlspecialchars($value) . '">' . htmlspecialchars(substr($value, 0, 50)) . '...</span>';
                                        } else {
                                            echo htmlspecialchars((string)$value);
                                        }
                                    ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <?php
                                    $primaryKeyColumn = 'id';
                                    foreach ($columns as $col) {
                                        if ($col['primary']) {
                                            $primaryKeyColumn = $col['name'];
                                            break;
                                        }
                                    }
                                    $primaryKeyValue = $row->{$primaryKeyColumn};
                                ?>
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="<?php echo e(route('admin.edit', [$table, $primaryKeyValue])); ?>" 
                                       class="text-blue-600 hover:text-blue-900 transition-colors" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="<?php echo e(route('admin.destroy', [$table, $primaryKeyValue])); ?>" 
                                          class="inline-block"
                                          onsubmit="return confirm('Are you sure you want to delete this row?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e(count($columns) + 1); ?>" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                No records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($rows->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <?php echo e($rows->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dzona/Downloads/crud/resources/views/admin/table.blade.php ENDPATH**/ ?>