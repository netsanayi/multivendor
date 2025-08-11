<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sistem Ayarları</h3>
                        <div class="card-tools">
                            <a href="<?php echo e(route('admin.settings.clear-cache')); ?>" class="btn btn-warning btn-sm"
                               onclick="return confirm('Önbelleği temizlemek istediğinize emin misiniz?');">
                                <i class="fas fa-broom"></i> Önbelleği Temizle
                            </a>
                            <a href="<?php echo e(route('admin.settings.optimize')); ?>" class="btn btn-info btn-sm"
                               onclick="return confirm('Uygulamayı optimize etmek istediğinize emin misiniz?');">
                                <i class="fas fa-rocket"></i> Optimize Et
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                                    <i class="fas fa-cog"></i> Genel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab">
                                    <i class="fas fa-address-book"></i> İletişim
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="social-tab" data-toggle="tab" href="#social" role="tab">
                                    <i class="fab fa-facebook"></i> Sosyal Medya
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="seo-tab" data-toggle="tab" href="#seo" role="tab">
                                    <i class="fas fa-search"></i> SEO
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab">
                                    <i class="fas fa-envelope"></i> E-posta
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="marketplace-tab" data-toggle="tab" href="#marketplace" role="tab">
                                    <i class="fas fa-store"></i> Marketplace
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab">
                                    <i class="fas fa-shield-alt"></i> Güvenlik
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content mt-3">
                            <!-- Genel Ayarlar -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="app_name">Uygulama Adı</label>
                                            <input type="text" class="form-control" id="app_name" 
                                                   name="settings[app_name]" 
                                                   value="<?php echo e($settings['general']['app_name'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="maintenance_mode">Bakım Modu</label>
                                            <select class="form-control" id="maintenance_mode" 
                                                    name="settings[maintenance_mode]">
                                                <option value="0" <?php echo e(($settings['general']['maintenance_mode'] ?? '0') == '0' ? 'selected' : ''); ?>>Kapalı</option>
                                                <option value="1" <?php echo e(($settings['general']['maintenance_mode'] ?? '0') == '1' ? 'selected' : ''); ?>>Açık</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="app_description">Uygulama Açıklaması</label>
                                    <textarea class="form-control" id="app_description" 
                                              name="settings[app_description]" rows="3"><?php echo e($settings['general']['app_description'] ?? ''); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="logo">Logo</label>
                                            <?php if($settings['general']['app_logo'] ?? false): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo e($settings['general']['app_logo']); ?>" alt="Logo" 
                                                         class="img-thumbnail" style="max-height: 100px;">
                                                </div>
                                            <?php endif; ?>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                                                <label class="custom-file-label" for="logo">Logo seç</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="favicon">Favicon</label>
                                            <?php if($settings['general']['app_favicon'] ?? false): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo e($settings['general']['app_favicon']); ?>" alt="Favicon" 
                                                         class="img-thumbnail" style="max-height: 32px;">
                                                </div>
                                            <?php endif; ?>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="favicon" name="favicon" accept="image/*">
                                                <label class="custom-file-label" for="favicon">Favicon seç</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- İletişim -->
                            <div class="tab-pane fade" id="contact" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_email">İletişim E-postası</label>
                                            <input type="email" class="form-control" id="contact_email" 
                                                   name="settings[contact_email]" 
                                                   value="<?php echo e($settings['contact']['contact_email'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_phone">İletişim Telefonu</label>
                                            <input type="text" class="form-control" id="contact_phone" 
                                                   name="settings[contact_phone]" 
                                                   value="<?php echo e($settings['contact']['contact_phone'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="contact_address">İletişim Adresi</label>
                                    <textarea class="form-control" id="contact_address" 
                                              name="settings[contact_address]" rows="3"><?php echo e($settings['contact']['contact_address'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <!-- Sosyal Medya -->
                            <div class="tab-pane fade" id="social" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_facebook">Facebook</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                                </div>
                                                <input type="url" class="form-control" id="social_facebook" 
                                                       name="settings[social_facebook]" 
                                                       value="<?php echo e($settings['social']['social_facebook'] ?? ''); ?>"
                                                       placeholder="https://facebook.com/...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_twitter">Twitter</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                                </div>
                                                <input type="url" class="form-control" id="social_twitter" 
                                                       name="settings[social_twitter]" 
                                                       value="<?php echo e($settings['social']['social_twitter'] ?? ''); ?>"
                                                       placeholder="https://twitter.com/...">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_instagram">Instagram</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                                </div>
                                                <input type="url" class="form-control" id="social_instagram" 
                                                       name="settings[social_instagram]" 
                                                       value="<?php echo e($settings['social']['social_instagram'] ?? ''); ?>"
                                                       placeholder="https://instagram.com/...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_youtube">YouTube</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fab fa-youtube"></i></span>
                                                </div>
                                                <input type="url" class="form-control" id="social_youtube" 
                                                       name="settings[social_youtube]" 
                                                       value="<?php echo e($settings['social']['social_youtube'] ?? ''); ?>"
                                                       placeholder="https://youtube.com/...">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="social_linkedin">LinkedIn</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                                </div>
                                                <input type="url" class="form-control" id="social_linkedin" 
                                                       name="settings[social_linkedin]" 
                                                       value="<?php echo e($settings['social']['social_linkedin'] ?? ''); ?>"
                                                       placeholder="https://linkedin.com/...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO -->
                            <div class="tab-pane fade" id="seo" role="tabpanel">
                                <div class="form-group">
                                    <label for="seo_title">SEO Başlık</label>
                                    <input type="text" class="form-control" id="seo_title" 
                                           name="settings[seo_title]" 
                                           value="<?php echo e($settings['seo']['seo_title'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="seo_description">SEO Açıklama</label>
                                    <textarea class="form-control" id="seo_description" 
                                              name="settings[seo_description]" rows="3"><?php echo e($settings['seo']['seo_description'] ?? ''); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="seo_keywords">SEO Anahtar Kelimeler</label>
                                    <input type="text" class="form-control" id="seo_keywords" 
                                           name="settings[seo_keywords]" 
                                           value="<?php echo e($settings['seo']['seo_keywords'] ?? ''); ?>"
                                           placeholder="anahtar1, anahtar2, anahtar3">
                                </div>

                                <div class="form-group">
                                    <label for="google_analytics">Google Analytics Kodu</label>
                                    <textarea class="form-control" id="google_analytics" 
                                              name="settings[google_analytics]" rows="4"
                                              placeholder="Google Analytics izleme kodunu buraya yapıştırın"><?php echo e($settings['seo']['google_analytics'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <!-- E-posta -->
                            <div class="tab-pane fade" id="email" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_driver">Mail Sürücüsü</label>
                                            <select class="form-control" id="mail_driver" name="settings[mail_driver]">
                                                <option value="smtp" <?php echo e(($settings['email']['mail_driver'] ?? 'smtp') == 'smtp' ? 'selected' : ''); ?>>SMTP</option>
                                                <option value="sendmail" <?php echo e(($settings['email']['mail_driver'] ?? '') == 'sendmail' ? 'selected' : ''); ?>>Sendmail</option>
                                                <option value="mailgun" <?php echo e(($settings['email']['mail_driver'] ?? '') == 'mailgun' ? 'selected' : ''); ?>>Mailgun</option>
                                                <option value="ses" <?php echo e(($settings['email']['mail_driver'] ?? '') == 'ses' ? 'selected' : ''); ?>>Amazon SES</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_host">Mail Sunucusu</label>
                                            <input type="text" class="form-control" id="mail_host" 
                                                   name="settings[mail_host]" 
                                                   value="<?php echo e($settings['email']['mail_host'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_port">Port</label>
                                            <input type="text" class="form-control" id="mail_port" 
                                                   name="settings[mail_port]" 
                                                   value="<?php echo e($settings['email']['mail_port'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_encryption">Şifreleme</label>
                                            <select class="form-control" id="mail_encryption" name="settings[mail_encryption]">
                                                <option value="">Yok</option>
                                                <option value="tls" <?php echo e(($settings['email']['mail_encryption'] ?? '') == 'tls' ? 'selected' : ''); ?>>TLS</option>
                                                <option value="ssl" <?php echo e(($settings['email']['mail_encryption'] ?? '') == 'ssl' ? 'selected' : ''); ?>>SSL</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_username">Kullanıcı Adı</label>
                                            <input type="text" class="form-control" id="mail_username" 
                                                   name="settings[mail_username]" 
                                                   value="<?php echo e($settings['email']['mail_username'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_password">Şifre</label>
                                            <input type="password" class="form-control" id="mail_password" 
                                                   name="settings[mail_password]" 
                                                   value="<?php echo e($settings['email']['mail_password'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_from_address">Gönderen E-posta</label>
                                            <input type="email" class="form-control" id="mail_from_address" 
                                                   name="settings[mail_from_address]" 
                                                   value="<?php echo e($settings['email']['mail_from_address'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mail_from_name">Gönderen Adı</label>
                                            <input type="text" class="form-control" id="mail_from_name" 
                                                   name="settings[mail_from_name]" 
                                                   value="<?php echo e($settings['email']['mail_from_name'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> E-posta Testi</h5>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="test_email" 
                                               placeholder="Test e-postası göndermek için e-posta adresi girin">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" id="test-email-btn">
                                                <i class="fas fa-paper-plane"></i> Test Gönder
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Marketplace -->
                            <div class="tab-pane fade" id="marketplace" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="commission_rate">Komisyon Oranı (%)</label>
                                            <input type="number" class="form-control" id="commission_rate" 
                                                   name="settings[commission_rate]" 
                                                   value="<?php echo e($settings['marketplace']['commission_rate'] ?? '10'); ?>"
                                                   min="0" max="100" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="min_payout_amount">Minimum Ödeme Tutarı (₺)</label>
                                            <input type="number" class="form-control" id="min_payout_amount" 
                                                   name="settings[min_payout_amount]" 
                                                   value="<?php echo e($settings['marketplace']['min_payout_amount'] ?? '100'); ?>"
                                                   min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="auto_approve_products">Ürünleri Otomatik Onayla</label>
                                            <select class="form-control" id="auto_approve_products" 
                                                    name="settings[auto_approve_products]">
                                                <option value="0" <?php echo e(($settings['marketplace']['auto_approve_products'] ?? '0') == '0' ? 'selected' : ''); ?>>Hayır</option>
                                                <option value="1" <?php echo e(($settings['marketplace']['auto_approve_products'] ?? '0') == '1' ? 'selected' : ''); ?>>Evet</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vendor_registration">Satıcı Kayıtlarına İzin Ver</label>
                                            <select class="form-control" id="vendor_registration" 
                                                    name="settings[vendor_registration]">
                                                <option value="1" <?php echo e(($settings['marketplace']['vendor_registration'] ?? '1') == '1' ? 'selected' : ''); ?>>Evet</option>
                                                <option value="0" <?php echo e(($settings['marketplace']['vendor_registration'] ?? '1') == '0' ? 'selected' : ''); ?>>Hayır</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Güvenlik -->
                            <div class="tab-pane fade" id="security" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="enable_2fa">İki Faktörlü Kimlik Doğrulama</label>
                                            <select class="form-control" id="enable_2fa" 
                                                    name="settings[enable_2fa]">
                                                <option value="0" <?php echo e(($settings['security']['enable_2fa'] ?? '0') == '0' ? 'selected' : ''); ?>>Devre Dışı</option>
                                                <option value="1" <?php echo e(($settings['security']['enable_2fa'] ?? '0') == '1' ? 'selected' : ''); ?>>Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_min_length">Minimum Şifre Uzunluğu</label>
                                            <input type="number" class="form-control" id="password_min_length" 
                                                   name="settings[password_min_length]" 
                                                   value="<?php echo e($settings['security']['password_min_length'] ?? '8'); ?>"
                                                   min="6" max="32">
                                        </div>
                                    </div>
                                </div>

                                <h5>Şifre Gereksinimleri</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="settings[password_require_uppercase]" value="0">
                                                <input type="checkbox" class="custom-control-input" id="password_require_uppercase"
                                                       name="settings[password_require_uppercase]" value="1"
                                                       <?php echo e(($settings['security']['password_require_uppercase'] ?? '1') == '1' ? 'checked' : ''); ?>>
                                                <label class="custom-control-label" for="password_require_uppercase">
                                                    Büyük harf gerekli
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="settings[password_require_numbers]" value="0">
                                                <input type="checkbox" class="custom-control-input" id="password_require_numbers"
                                                       name="settings[password_require_numbers]" value="1"
                                                       <?php echo e(($settings['security']['password_require_numbers'] ?? '1') == '1' ? 'checked' : ''); ?>>
                                                <label class="custom-control-label" for="password_require_numbers">
                                                    Rakam gerekli
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="settings[password_require_symbols]" value="0">
                                                <input type="checkbox" class="custom-control-input" id="password_require_symbols"
                                                       name="settings[password_require_symbols]" value="1"
                                                       <?php echo e(($settings['security']['password_require_symbols'] ?? '1') == '1' ? 'checked' : ''); ?>>
                                                <label class="custom-control-label" for="password_require_symbols">
                                                    Sembol gerekli
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_login_attempts">Maksimum Giriş Denemesi</label>
                                            <input type="number" class="form-control" id="max_login_attempts" 
                                                   name="settings[max_login_attempts]" 
                                                   value="<?php echo e($settings['security']['max_login_attempts'] ?? '5'); ?>"
                                                   min="3" max="10">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lockout_duration">Kilitlenme Süresi (Dakika)</label>
                                            <input type="number" class="form-control" id="lockout_duration" 
                                                   name="settings[lockout_duration]" 
                                                   value="<?php echo e($settings['security']['lockout_duration'] ?? '60'); ?>"
                                                   min="15" max="1440">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Ayarları Kaydet
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Başarı ve hata mesajlarını göster
        <?php if(session('success')): ?>
            toastr.success('<?php echo e(session('success')); ?>');
        <?php endif; ?>
        <?php if(session('error')): ?>
            toastr.error('<?php echo e(session('error')); ?>');
        <?php endif; ?>

        // Dosya seçildiğinde dosya adını göster
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass('selected').html(fileName);
        });

        // E-posta testi
        $('#test-email-btn').on('click', function() {
            let email = $('#test_email').val();
            
            if (!email) {
                toastr.error('Lütfen bir e-posta adresi girin');
                return;
            }

            let btn = $(this);
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...');

            $.ajax({
                url: '<?php echo e(route("admin.settings.test-email")); ?>',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    test_email: email
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('E-posta gönderilemedi: ' + xhr.responseJSON.message);
                },
                complete: function() {
                    btn.prop('disabled', false);
                    btn.html('<i class="fas fa-paper-plane"></i> Test Gönder');
                }
            });
        });

        // Tab state'i sakla
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            localStorage.setItem('activeSettingsTab', $(e.target).attr('href'));
        });

        // Sayfa yüklendiğinde aktif tab'ı göster
        var activeTab = localStorage.getItem('activeSettingsTab');
        if (activeTab) {
            $('#settingsTabs a[href="' + activeTab + '"]').tab('show');
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Murat\Projects\laravelmv\resources\views/settings/index.blade.php ENDPATH**/ ?>