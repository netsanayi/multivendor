<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Blog Yazıları</h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('admin.blogs.create')); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Yeni Blog Yazısı Ekle
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Arama ve Filtreleme -->
                    <form method="GET" action="<?php echo e(route('admin.blogs.index')); ?>" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Başlık veya içerikte ara..." 
                                           value="<?php echo e(request('search')); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">Tüm Durumlar</option>
                                        <option value="1" <?php echo e(request('status') == '1' ? 'selected' : ''); ?>>Yayında</option>
                                        <option value="0" <?php echo e(request('status') == '0' ? 'selected' : ''); ?>>Taslak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Ara
                                </button>
                                <a href="<?php echo e(route('admin.blogs.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Temizle
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Blog Yazıları Tablosu -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Başlık</th>
                                    <th width="200">Yayın Tarihi</th>
                                    <th width="100">Durum</th>
                                    <th width="150">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($blog->id); ?></td>
                                    <td>
                                        <?php echo e($blog->title); ?>

                                        <br>
                                        <small class="text-muted"><?php echo e(Str::limit(strip_tags($blog->description), 100)); ?></small>
                                    </td>
                                    <td><?php echo e($blog->created_at->format('d.m.Y H:i')); ?></td>
                                    <td>
                                        <?php if($blog->status): ?>
                                            <span class="badge badge-success">Yayında</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Taslak</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('admin.blogs.show', $blog)); ?>" 
                                           class="btn btn-info btn-sm" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.blogs.edit', $blog)); ?>" 
                                           class="btn btn-warning btn-sm" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.blogs.destroy', $blog)); ?>" 
                                              method="POST" class="d-inline-block" 
                                              onsubmit="return confirm('Bu blog yazısını silmek istediğinize emin misiniz?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Henüz blog yazısı eklenmemiş.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Sayfalama -->
                    <div class="mt-3">
                        <?php echo e($blogs->withQueryString()->links()); ?>

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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Murat\Projects\laravelmv\resources\views/blogs/index.blade.php ENDPATH**/ ?>