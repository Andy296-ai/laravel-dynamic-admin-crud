<?php $__env->startSection('title', 'Edit Row - ' . ucfirst($table)); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Row
                </h2>
                <a href="<?php echo e(route('admin.table', $table)); ?>" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Table
                </a>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Table: <span class="font-semibold"><?php echo e($table); ?></span> | 
                ID: <span class="font-semibold"><?php echo e($id); ?></span>
            </p>
        </div>
        
        <!-- Form -->
        <form method="POST" action="<?php echo e(route('admin.update', [$table, $id])); ?>" class="p-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="space-y-6">
                <?php $__currentLoopData = $formColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <label for="<?php echo e($column['name']); ?>" class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo e(ucfirst(str_replace('_', ' ', $column['name']))); ?>

                            <?php if($column['name'] === $primaryKey): ?>
                                <span class="text-xs text-gray-500 font-normal ml-2">(Primary Key)</span>
                            <?php endif; ?>
                            <?php if(!$column['nullable'] && $column['default'] === null && $column['name'] !== $primaryKey): ?>
                                <span class="text-red-500">*</span>
                            <?php endif; ?>
                            <span class="text-xs text-gray-500 font-normal ml-2">(<?php echo e($column['type']); ?>)</span>
                        </label>
                        
                        <?php
                            $inputType = 'text';
                            $inputClass = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500';
                            $value = old($column['name'], $row->{$column['name']});
                            
                            // Determine input type based on column type
                            if (strpos($column['type'], 'int') !== false) {
                                $inputType = 'number';
                                $inputClass .= ' number-input';
                            } elseif (strpos($column['type'], 'decimal') !== false || strpos($column['type'], 'float') !== false || strpos($column['type'], 'double') !== false) {
                                $inputType = 'number';
                                $inputClass .= ' number-input';
                                $step = 'step="0.01"';
                            } elseif (strpos($column['type'], 'date') !== false && strpos($column['type'], 'time') === false) {
                                $inputType = 'date';
                                if ($value && $value !== '0000-00-00') {
                                    $value = date('Y-m-d', strtotime($value));
                                }
                            } elseif (strpos($column['type'], 'datetime') !== false || strpos($column['type'], 'timestamp') !== false) {
                                $inputType = 'datetime-local';
                                if ($value && $value !== '0000-00-00 00:00:00') {
                                    $value = date('Y-m-d\TH:i', strtotime($value));
                                }
                            } elseif (strpos($column['type'], 'time') !== false) {
                                $inputType = 'time';
                            } elseif ($column['type'] === 'tinyint' && $column['max_length'] == 1) {
                                $inputType = 'checkbox';
                            } elseif (strpos($column['type'], 'text') !== false || ($column['max_length'] && $column['max_length'] > 255)) {
                                $inputType = 'textarea';
                            } elseif ($column['name'] === 'email' || strpos($column['name'], 'email') !== false) {
                                $inputType = 'email';
                            } elseif ($column['name'] === 'password' || strpos($column['name'], 'password') !== false) {
                                $inputType = 'password';
                            }
                        ?>
                        
                        <?php if($column['name'] === $primaryKey): ?>
                            <!-- Primary key is read-only -->
                            <input 
                                type="text" 
                                value="<?php echo e($value); ?>"
                                class="<?php echo e($inputClass); ?> bg-gray-100 cursor-not-allowed"
                                readonly
                                disabled
                            >
                            <input type="hidden" name="<?php echo e($column['name']); ?>" value="<?php echo e($value); ?>">
                        <?php elseif($inputType === 'textarea'): ?>
                            <textarea 
                                name="<?php echo e($column['name']); ?>" 
                                id="<?php echo e($column['name']); ?>" 
                                rows="4"
                                class="<?php echo e($inputClass); ?> <?php $__errorArgs = [$column['name']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                <?php if(!$column['nullable'] && $column['default'] === null): ?> required <?php endif; ?>
                                <?php if($column['max_length']): ?> maxlength="<?php echo e($column['max_length']); ?>" <?php endif; ?>
                            ><?php echo e($value); ?></textarea>
                        <?php elseif($inputType === 'checkbox'): ?>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="<?php echo e($column['name']); ?>" 
                                        id="<?php echo e($column['name']); ?>" 
                                        value="1"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        <?php echo e($value ? 'checked' : ''); ?>

                                    >
                                    <span class="ml-2 text-sm text-gray-600">Yes</span>
                                </label>
                            </div>
                        <?php else: ?>
                            <input 
                                type="<?php echo e($inputType); ?>" 
                                name="<?php echo e($column['name']); ?>" 
                                id="<?php echo e($column['name']); ?>" 
                                value="<?php echo e($value); ?>"
                                class="<?php echo e($inputClass); ?> <?php $__errorArgs = [$column['name']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                <?php if(!$column['nullable'] && $column['default'] === null && $column['name'] !== $primaryKey): ?> required <?php endif; ?>
                                <?php if($column['max_length'] && $inputType !== 'number'): ?> maxlength="<?php echo e($column['max_length']); ?>" <?php endif; ?>
                                <?php if(isset($step)): ?> <?php echo $step; ?> <?php endif; ?>
                                <?php if($inputType === 'number' && strpos($column['type'], 'unsigned') !== false): ?> min="0" <?php endif; ?>
                            >
                        <?php endif; ?>
                        
                        <?php $__errorArgs = [$column['name']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <?php if($column['nullable']): ?>
                            <p class="mt-1 text-xs text-gray-500">Optional</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            
            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="<?php echo e(route('admin.table', $table)); ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Row
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dzona/Downloads/crud/resources/views/admin/edit.blade.php ENDPATH**/ ?>