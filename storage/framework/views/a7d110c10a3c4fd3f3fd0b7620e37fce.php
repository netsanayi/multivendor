<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bannerlar</h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('admin.banners.create')); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Yeni Banner Ekle
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Arama ve Filtreleme -->
                    <form method="GET" action="<?php echo e(route('admin.banners.index')); ?>" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Banner adı ara..." 
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
                                <a href="<?php echo e(route('admin.banners.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Temizle
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Bannerlar Grid -->
                    <div class="row">
                        <?php $__empty_1 = true; $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="<?php echo e($banner->image->url); ?>" class="card-img-top" alt="<?php echo e($banner->name); ?>" 
                                     style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo e($banner->name); ?></h5>
                                    <?php if($banner->link): ?>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-link"></i> <?php echo e(Str::limit($banner->link, 30)); ?>

                                            </small>
                                        </p>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php if($banner->status): ?>
                                                <span class="badge badge-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Pasif</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('admin.banners.show', $banner)); ?>" 
                                               class="btn btn-info btn-sm" title="Görüntüle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.banners.edit', $banner)); ?>" 
                                               class="btn btn-warning btn-sm" title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm toggle-status"
                                                    data-id="<?php echo e($banner->id); ?>"
                                                    data-status="<?php echo e($banner->status); ?>"
                                                    title="Durumu Değiştir">
                                                <?php if($banner->status): ?>
                                                    <i class="fas fa-toggle-on text-success"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-toggle-off text-danger"></i>
                                                <?php endif; ?>
                                            </button>
                                            <form action="<?php echo e(route('admin.banners.destroy', $banner)); ?>" 
                                                  method="POST" class="d-inline-block" 
                                                  onsubmit="return confirm('Bu banner\'ı silmek istediğinize emin misiniz?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    <small><?php echo e($banner->created_at->format('d.m.Y H:i')); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> Henüz banner eklenmemiş.
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sayfalama -->
                    <div class="mt-3">
                        <?php echo e($banners->withQueryString()->links()); ?>

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

    // Durum değiştirme
    $('.toggle-status').on('click', function() {
        let btn = $(this);
        let bannerId = btn.data('id');
        
        $.ajax({
            url: '/admin/banners/' + bannerId + '/toggle-status',
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    
                    // İkonu güncelle
                    if (response.status) {
                        btn.html('<i class="fas fa-toggle-on text-success"></i>');
                        btn.closest('.card').find('.badge').removeClass('badge-danger').addClass('badge-success').text('Aktif');
                    } else {
                        btn.html('<i class="fas fa-toggle-off text-danger"></i>');
                        btn.closest('.card').find('.badge').removeClass('badge-success').addClass('badge-danger').text('Pasif');
                    }
                    
                    btn.data('status', response.status);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Durum güncellenirken bir hata oluştu.');
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Murat\Projects\laravelmv\resources\views/banners/index.blade.php ENDPATH**/ ?>