<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Adresler</h3>
                    <div class="card-tools">
                        <form action="<?php echo e(route('admin.addresses.index')); ?>" method="GET" class="form-inline">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control float-right" 
                                       placeholder="Adres ara..." value="<?php echo e(request('search')); ?>">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <a href="<?php echo e(route('admin.addresses.create')); ?>" class="btn btn-primary btn-sm float-right mr-2">
                        <i class="fas fa-plus"></i> Yeni Adres Ekle
                    </a>
                </div>
                <div class="card-body">
                    <?php if($addresses->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Kullanıcı</th>
                                    <th>Adres Adı</th>
                                    <th>Adres</th>
                                    <th>Tür</th>
                                    <th width="100">Durum</th>
                                    <th width="150">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($address->id); ?></td>
                                    <td>
                                        <?php if($address->user): ?>
                                            <a href="<?php echo e(route('admin.users.show', $address->user)); ?>">
                                                <?php echo e($address->user->first_name); ?> <?php echo e($address->user->last_name); ?>

                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($address->address_name); ?></td>
                                    <td>
                                        <?php echo e($address->street); ?> <?php echo e($address->road_name); ?><br>
                                        No: <?php echo e($address->building_no); ?><?php echo e($address->door_no ? '/' . $address->door_no : ''); ?>

                                        <?php echo e($address->floor ? 'Kat: ' . $address->floor : ''); ?><br>
                                        <?php echo e($address->district); ?> / <?php echo e($address->city); ?>

                                    </td>
                                    <td>
                                        <?php if($address->company_type == 'corporate'): ?>
                                            <span class="badge badge-primary">Kurumsal</span>
                                            <br><small><?php echo e($address->company_name); ?></small>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Bireysel</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($address->status): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Pasif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('admin.addresses.show', $address)); ?>" 
                                           class="btn btn-info btn-sm" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.addresses.edit', $address)); ?>" 
                                           class="btn btn-warning btn-sm" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.addresses.destroy', $address)); ?>" 
                                              method="POST" class="d-inline-block" 
                                              onsubmit="return confirm('Bu adresi silmek istediğinize emin misiniz?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        <?php echo e($addresses->appends(request()->query())->links()); ?>

                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Henüz adres bulunmuyor.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Başarı ve hata mesajlarını göster
    <?php if(session('success')): ?>
        toastr.success('<?php echo e(session('success')); ?>');
    <?php endif; ?>
    <?php if(session('error')): ?>
        toastr.error('<?php echo e(session('error')); ?>');
    <?php endif; ?>
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Murat\Projects\laravelmv\resources\views/addresses/index.blade.php ENDPATH**/ ?>