<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Roller</h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('admin.roles.create')); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Yeni Rol Ekle
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Arama ve Filtreleme -->
                    <form method="GET" action="<?php echo e(route('admin.roles.index')); ?>" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Rol adı ara..." 
                                           value="<?php echo e(request('search')); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">Tüm Durumlar</option>
                                        <option value="1" <?php echo e(request('status') == '1' ? 'selected' : ''); ?>>Aktif</option>
                                        <option value="0" <?php echo e(request('status') == '0' ? 'selected' : ''); ?>>Pasif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Ara
                                </button>
                                <a href="<?php echo e(route('admin.roles.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Temizle
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Roller Tablosu -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Rol Adı</th>
                                    <th>Kullanıcı Sayısı</th>
                                    <th width="100">Durum</th>
                                    <th width="150">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($role->id); ?></td>
                                    <td>
                                        <?php echo e($role->name); ?>

                                        <?php if(in_array($role->name, ['Admin', 'Vendor', 'Customer'])): ?>
                                            <span class="badge badge-info ml-1">Sistem Rolü</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($role->users_count ?? 0); ?></td>
                                    <td>
                                        <?php if($role->status): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Pasif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('admin.roles.show', $role)); ?>" 
                                           class="btn btn-info btn-sm" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.roles.edit', $role)); ?>" 
                                           class="btn btn-warning btn-sm" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if(!in_array($role->name, ['Admin', 'Vendor', 'Customer'])): ?>
                                        <form action="<?php echo e(route('admin.roles.destroy', $role)); ?>" 
                                              method="POST" class="d-inline-block" 
                                              onsubmit="return confirm('Bu rolü silmek istediğinize emin misiniz?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Henüz rol eklenmemiş.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Sayfalama -->
                    <div class="mt-3">
                        <?php echo e($roles->withQueryString()->links()); ?>

                    </div>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Murat\Projects\laravelmv\resources\views/roles/index.blade.php ENDPATH**/ ?>