<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Aktivite Logları</h1>
                <div>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-download"></i> Dışa Aktar
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearModal">
                        <i class="fas fa-trash"></i> Eski Logları Temizle
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.activity-log.index')); ?>" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Ara</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo e(request('search')); ?>" placeholder="Açıklama veya özellik...">
                </div>
                <div class="col-md-2">
                    <label for="log_name" class="form-label">Log Tipi</label>
                    <select class="form-select" id="log_name" name="log_name">
                        <option value="">Tümü</option>
                        <?php $__currentLoopData = $logNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $logName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($logName); ?>" 
                                <?php echo e(request('log_name') == $logName ? 'selected' : ''); ?>>
                                <?php echo e($logName ?: 'Genel'); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="subject_type" class="form-label">Konu Tipi</label>
                    <select class="form-select" id="subject_type" name="subject_type">
                        <option value="">Tümü</option>
                        <?php $__currentLoopData = $subjectTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type['value']); ?>" 
                                <?php echo e(request('subject_type') == $type['value'] ? 'selected' : ''); ?>>
                                <?php echo e($type['label']); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="start_date" class="form-label">Başlangıç</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="<?php echo e(request('start_date')); ?>">
                </div>
                <div class="col-md-2">
                    <label for="end_date" class="form-label">Bitiş</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="<?php echo e(request('end_date')); ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="<?php echo e(route('admin.activity-log.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Loglar Tablosu -->
    <div class="card">
        <div class="card-body">
            <?php if($activities->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Açıklama</th>
                                <th>Konu</th>
                                <th>Kullanıcı</th>
                                <th width="100">Log Tipi</th>
                                <th width="150">Tarih</th>
                                <th width="80">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($activity->id); ?></td>
                                    <td>
                                        <?php echo e($activity->description); ?>

                                        <?php if($activity->event): ?>
                                            <span class="badge bg-info ms-1"><?php echo e($activity->event); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($activity->subject): ?>
                                            <small class="text-muted">
                                                <?php echo e(class_basename($activity->subject_type)); ?>

                                                #<?php echo e($activity->subject_id); ?>

                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($activity->causer): ?>
                                            <?php echo e($activity->causer->name); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Sistem</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo e($activity->log_name ?: 'default'); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo e($activity->created_at->format('d.m.Y H:i')); ?>

                                            <br>
                                            <span class="text-muted">
                                                <?php echo e($activity->created_at->diffForHumans()); ?>

                                            </span>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('admin.activity-log.show', $activity)); ?>" 
                                           class="btn btn-sm btn-info" title="Detaylar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($activities->withQueryString()->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aktivite logu bulunamadı</h5>
                    <p class="text-muted">Seçili filtrelere uygun log kaydı yok.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.activity-log.export')); ?>" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">Logları Dışa Aktar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Mevcut filtrelere göre loglar CSV formatında dışa aktarılacaktır.</p>
                    
                    <?php if(request()->anyFilled(['search', 'log_name', 'subject_type', 'start_date', 'end_date'])): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            Aktif filtreler uygulanacak
                        </div>
                        
                        <!-- Mevcut filtreleri gizli inputlar olarak ekle -->
                        <?php $__currentLoopData = request()->only(['search', 'log_name', 'subject_type', 'start_date', 'end_date']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($value): ?>
                                <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download"></i> Dışa Aktar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Clear Modal -->
<div class="modal fade" id="clearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.activity-log.clear')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Eski Logları Temizle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="days" class="form-label">Kaç günden eski loglar silinsin?</label>
                        <input type="number" class="form-control" id="days" name="days" 
                               value="30" min="1" required>
                        <small class="form-text text-muted">
                            Belirtilen gün sayısından eski tüm loglar kalıcı olarak silinecektir.
                        </small>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Dikkat!</strong> Bu işlem geri alınamaz.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Temizle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Tablo satırlarına hover efekti
    document.querySelectorAll('tbody tr').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function(e) {
            if (!e.target.closest('a') && !e.target.closest('button')) {
                const showUrl = this.querySelector('a[title="Detaylar"]')?.href;
                if (showUrl) {
                    window.location.href = showUrl;
                }
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Murat\Projects\laravelmv\resources\views/admin/activity-log/index.blade.php ENDPATH**/ ?>