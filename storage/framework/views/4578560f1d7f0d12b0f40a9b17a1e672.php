<?php $__env->startSection('content'); ?>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Bildirimler</h2>
                    <div class="flex space-x-2">
                        <?php if($notifications->where('read_at', null)->count() > 0): ?>
                            <form method="POST" action="<?php echo e(route('notifications.read-all')); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    Tümünü okundu işaretle
                                </button>
                            </form>
                        <?php endif; ?>
                        <?php if($notifications->count() > 0): ?>
                            <form method="POST" action="<?php echo e(route('notifications.clear')); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" onclick="return confirm('Tüm bildirimleri silmek istediğinizden emin misiniz?')" class="text-sm text-red-600 hover:text-red-900">
                                    Tümünü temizle
                                </button>
                            </form>
                        <?php endif; ?>
                        <a href="<?php echo e(route('notifications.settings')); ?>" class="text-sm text-gray-600 hover:text-gray-900">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Ayarlar
                        </a>
                    </div>
                </div>

                <?php if($notifications->isEmpty()): ?>
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Bildirim yok</h3>
                        <p class="mt-1 text-sm text-gray-500">Yeni bir bildiriminiz olduğunda burada görünecek.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start space-x-3 p-4 <?php echo e($notification->read_at ? 'bg-white' : 'bg-blue-50'); ?> rounded-lg border <?php echo e($notification->read_at ? 'border-gray-200' : 'border-blue-200'); ?>">
                            <div class="flex-shrink-0">
                                <?php
                                    $icon = 'bell';
                                    $iconColor = 'text-gray-400';
                                    
                                    if(str_contains($notification->type, 'Order')) {
                                        $icon = 'shopping-cart';
                                        $iconColor = 'text-green-500';
                                    } elseif(str_contains($notification->type, 'Product')) {
                                        $icon = 'package';
                                        $iconColor = 'text-blue-500';
                                    } elseif(str_contains($notification->type, 'Message')) {
                                        $icon = 'mail';
                                        $iconColor = 'text-purple-500';
                                    } elseif(str_contains($notification->type, 'Payment')) {
                                        $icon = 'credit-card';
                                        $iconColor = 'text-yellow-500';
                                    }
                                ?>
                                
                                <?php if($icon == 'bell'): ?>
                                    <svg class="w-6 h-6 <?php echo e($iconColor); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                <?php elseif($icon == 'shopping-cart'): ?>
                                    <svg class="w-6 h-6 <?php echo e($iconColor); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                <?php elseif($icon == 'package'): ?>
                                    <svg class="w-6 h-6 <?php echo e($iconColor); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                <?php elseif($icon == 'mail'): ?>
                                    <svg class="w-6 h-6 <?php echo e($iconColor); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                <?php elseif($icon == 'credit-card'): ?>
                                    <svg class="w-6 h-6 <?php echo e($iconColor); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo e($notification->data['title'] ?? 'Bildirim'); ?>

                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    <?php echo e($notification->data['message'] ?? ''); ?>

                                </p>
                                <?php if(isset($notification->data['action_url'])): ?>
                                    <a href="<?php echo e($notification->data['action_url']); ?>" class="mt-2 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                        Görüntüle
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <p class="text-xs text-gray-400 mt-1">
                                    <?php echo e($notification->created_at->diffForHumans()); ?>

                                </p>
                            </div>
                            
                            <div class="flex-shrink-0 flex items-center space-x-2">
                                <?php if(!$notification->read_at): ?>
                                    <form method="POST" action="<?php echo e(route('notifications.read', $notification->id)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-900">
                                            Okundu işaretle
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" action="<?php echo e(route('notifications.destroy', $notification->id)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="mt-6">
                        <?php echo e($notifications->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Murat\Projects\laravelmv\resources\views/notifications/index.blade.php ENDPATH**/ ?>