<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title><?php echo $__env->yieldContent('title', 'Admin Panel'); ?> - <?php echo e(config('app.name', 'Laravel')); ?></title>

    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('favicon_logo.svg')); ?>">
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center gap-3">
                    <img src="<?php echo e(asset('favicon_logo.svg')); ?>" alt="Logo" class="h-9 w-9 rounded bg-white/10 p-1">
                    <div class="min-w-0">
                        <div class="text-xl font-bold leading-tight">Admin Panel</div>
                        <div class="text-xs text-gray-300 truncate"><?php echo e(config('app.name', 'Laravel')); ?></div>
                    </div>
                </div>
            </div>
            
            <nav class="flex-1 overflow-y-auto p-4">
                <div class="mb-4">
                    <a href="<?php echo e(route('admin.index')); ?>" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-table mr-3"></i>
                        <span>All Tables</span>
                    </a>
                </div>
                
                <?php if(isset($tableNames)): ?>
                    <div class="mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-2">
                        Database Tables
                    </div>
                    <ul class="space-y-1">
                        <?php $__currentLoopData = $tableNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tableName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a href="<?php echo e(route('admin.table', $tableName)); ?>" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors <?php echo e(request()->route('table') === $tableName ? 'bg-gray-700' : ''); ?>">
                                    <i class="fas fa-database mr-3 text-sm"></i>
                                    <span class="truncate"><?php echo e($tableName); ?></span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            </nav>
            
            <div class="p-4 border-t border-gray-700">
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="w-full flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <div class="p-6">
                <!-- Flash Messages -->
                <?php if(session('success')): ?>
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                            <i class="fas fa-times cursor-pointer"></i>
                        </span>
                    </div>
                <?php endif; ?>
                
                <?php if(session('error')): ?>
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo e(session('error')); ?></span>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                            <i class="fas fa-times cursor-pointer"></i>
                        </span>
                    </div>
                <?php endif; ?>
                
                <?php if($errors->any()): ?>
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                            <i class="fas fa-times cursor-pointer"></i>
                        </span>
                    </div>
                <?php endif; ?>
                
                <!-- Page Content -->
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>
    </div>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/dzona/Downloads/crud/resources/views/layouts/admin.blade.php ENDPATH**/ ?>