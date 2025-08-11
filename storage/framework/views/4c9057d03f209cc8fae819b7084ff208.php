<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Yeni Rol Ekle</h3>
                </div>
                <form action="<?php echo e(route('admin.roles.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Rol Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="name" name="name" value="<?php echo e(old('name')); ?>" required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Durum <span class="text-danger">*</span></label>
                                    <select class="form-control <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="status" name="status" required>
                                        <option value="1" <?php echo e(old('status', 1) == 1 ? 'selected' : ''); ?>>Aktif</option>
                                        <option value="0" <?php echo e(old('status') == 0 ? 'selected' : ''); ?>>Pasif</option>
                                    </select>
                                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">İzinler</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Modül</th>
                                        <th width="100" class="text-center">
                                            <label class="mb-0">
                                                <input type="checkbox" class="check-all" data-action="view"> Görüntüle
                                            </label>
                                        </th>
                                        <th width="100" class="text-center">
                                            <label class="mb-0">
                                                <input type="checkbox" class="check-all" data-action="create"> Oluştur
                                            </label>
                                        </th>
                                        <th width="100" class="text-center">
                                            <label class="mb-0">
                                                <input type="checkbox" class="check-all" data-action="edit"> Düzenle
                                            </label>
                                        </th>
                                        <th width="100" class="text-center">
                                            <label class="mb-0">
                                                <input type="checkbox" class="check-all" data-action="delete"> Sil
                                            </label>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($module['name']); ?></strong>
                                            <input type="checkbox" class="check-module ml-2" data-module="<?php echo e($key); ?>">
                                        </td>
                                        <?php $__currentLoopData = $module['permissions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center">
                                            <?php if(in_array($permission, ['view', 'create', 'edit', 'delete'])): ?>
                                                <input type="checkbox" 
                                                       name="permissions[<?php echo e($key); ?>][]" 
                                                       value="<?php echo e($permission); ?>"
                                                       data-module="<?php echo e($key); ?>"
                                                       data-action="<?php echo e($permission); ?>"
                                                       class="permission-check"
                                                       <?php echo e(is_array(old("permissions.$key")) && in_array($permission, old("permissions.$key")) ? 'checked' : ''); ?>>
                                            <?php endif; ?>
                                        </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(count($module['permissions']) < 4): ?>
                                            <?php for($i = count($module['permissions']); $i < 4; $i++): ?>
                                                <td class="text-center">-</td>
                                            <?php endfor; ?>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php $__errorArgs = ['permissions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="text-danger mt-2"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <a href="<?php echo e(route('admin.roles.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Geri Dön
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Tümünü seç checkboxları
        $('.check-all').on('change', function() {
            var action = $(this).data('action');
            var isChecked = $(this).is(':checked');
            
            $('.permission-check[data-action="' + action + '"]').prop('checked', isChecked);
        });

        // Modül checkboxları
        $('.check-module').on('change', function() {
            var module = $(this).data('module');
            var isChecked = $(this).is(':checked');
            
            $('.permission-check[data-module="' + module + '"]').prop('checked', isChecked);
        });

        // İzin checkbox değişimi
        $('.permission-check').on('change', function() {
            updateCheckAllStates();
            updateModuleCheckStates();
        });

        // Sayfa yüklendiğinde durumları güncelle
        updateCheckAllStates();
        updateModuleCheckStates();

        function updateCheckAllStates() {
            ['view', 'create', 'edit', 'delete'].forEach(function(action) {
                var total = $('.permission-check[data-action="' + action + '"]').length;
                var checked = $('.permission-check[data-action="' + action + '"]:checked').length;
                
                if (checked === 0) {
                    $('.check-all[data-action="' + action + '"]').prop('checked', false).prop('indeterminate', false);
                } else if (checked === total) {
                    $('.check-all[data-action="' + action + '"]').prop('checked', true).prop('indeterminate', false);
                } else {
                    $('.check-all[data-action="' + action + '"]').prop('checked', false).prop('indeterminate', true);
                }
            });
        }

        function updateModuleCheckStates() {
            $('.check-module').each(function() {
                var module = $(this).data('module');
                var total = $('.permission-check[data-module="' + module + '"]').length;
                var checked = $('.permission-check[data-module="' + module + '"]:checked').length;
                
                if (checked === 0) {
                    $(this).prop('checked', false).prop('indeterminate', false);
                } else if (checked === total) {
                    $(this).prop('checked', true).prop('indeterminate', false);
                } else {
                    $(this).prop('checked', false).prop('indeterminate', true);
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Murat\Projects\laravelmv\resources\views/roles/create.blade.php ENDPATH**/ ?>