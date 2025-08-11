<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Diller</h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('admin.languages.create')); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Yeni Dil Ekle
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Arama ve Filtreleme -->
                    <form method="GET" action="<?php echo e(route('admin.languages.index')); ?>" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Dil adı veya kod ara..." 
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
                                <a href="<?php echo e(route('admin.languages.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Temizle
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Diller Tablosu -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="languages-table">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <i class="fas fa-grip-vertical"></i>
                                    </th>
                                    <th width="50">ID</th>
                                    <th width="60">Bayrak</th>
                                    <th>Dil Adı</th>
                                    <th width="100">Kod</th>
                                    <th width="200">Locale</th>
                                    <th width="100">Durum</th>
                                    <th width="150">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr data-id="<?php echo e($language->id); ?>">
                                    <td class="handle">
                                        <i class="fas fa-grip-vertical"></i>
                                    </td>
                                    <td><?php echo e($language->id); ?></td>
                                    <td>
                                        <?php if($language->image): ?>
                                            <img src="<?php echo e($language->image->url); ?>" alt="<?php echo e($language->name); ?>" 
                                                 class="img-thumbnail" style="max-width: 40px;">
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($language->name); ?>

                                        <?php if($language->code == 'tr'): ?>
                                            <span class="badge badge-info ml-1">Varsayılan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($language->code); ?></td>
                                    <td><code><?php echo e($language->locale); ?></code></td>
                                    <td>
                                        <?php if($language->status): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Pasif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('admin.languages.show', $language)); ?>" 
                                           class="btn btn-info btn-sm" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.languages.edit', $language)); ?>" 
                                           class="btn btn-warning btn-sm" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if($language->code != 'tr'): ?>
                                        <form action="<?php echo e(route('admin.languages.destroy', $language)); ?>" 
                                              method="POST" class="d-inline-block" 
                                              onsubmit="return confirm('Bu dili silmek istediğinize emin misiniz? Dil dosyaları da silinecektir.');">
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
                                    <td colspan="8" class="text-center">Henüz dil eklenmemiş.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Sayfalama -->
                    <div class="mt-3">
                        <?php echo e($languages->withQueryString()->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .handle {
        cursor: move;
    }
    .ui-sortable-helper {
        background-color: #f8f9fa;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
    // Başarı ve hata mesajlarını göster
    <?php if(session('success')): ?>
        toastr.success('<?php echo e(session('success')); ?>');
    <?php endif; ?>
    <?php if(session('error')): ?>
        toastr.error('<?php echo e(session('error')); ?>');
    <?php endif; ?>

    // Sıralanabilir tablo
    $(document).ready(function() {
        $("#languages-table tbody").sortable({
            handle: ".handle",
            update: function(event, ui) {
                let order = [];
                $('#languages-table tbody tr').each(function(index) {
                    order.push({
                        id: $(this).data('id'),
                        order: index
                    });
                });

                // AJAX ile sıralamayı güncelle
                $.ajax({
                    url: '<?php echo e(route("admin.languages.update-order")); ?>',
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        languages: order
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Sıralama güncellenirken bir hata oluştu.');
                    }
                });
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Murat\Projects\laravelmv\resources\views/languages/index.blade.php ENDPATH**/ ?>