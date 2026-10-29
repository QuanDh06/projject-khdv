<?php
/**
 * Local variables.
 *
 * @var bool $display_login_button
 * @var string $legal_notice_url
 * @var string $imprint_url
 */
?>

<div id="frame-footer" class="px-4 py-3 border-top bg-light rounded-bottom">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="text-muted small">
            <i class="fas fa-hotel me-1 text-primary"></i>
            <span class="fw-medium"><?= e(setting('company_name')) ?></span>

            <?php if (!empty($legal_notice_url)): ?>
                <span class="mx-1">|</span>
                <a href="<?= e($legal_notice_url) ?>" target="_blank" class="text-decoration-none"><?= lang('legal_notice') ?></a>
            <?php endif; ?>

            <?php if (!empty($imprint_url)): ?>
                <span class="mx-1">|</span>
                <a href="<?= e($imprint_url) ?>" target="_blank" class="text-decoration-none"><?= lang('imprint') ?></a>
            <?php endif; ?>
        </div>

        <div class="footer-options d-flex align-items-center gap-2">
            <span id="select-language" class="badge rounded-pill bg-secondary px-3 py-2" style="cursor: pointer; font-weight: 500;">
                <i class="fas fa-globe me-1"></i>
                <?= ucfirst(config('language')) ?>
            </span>

            <?php if ($display_login_button): ?>
                <a class="backend-link badge rounded-pill bg-primary text-decoration-none px-3 py-2"
                   href="<?= session('user_id') ? site_url('calendar') : site_url('login') ?>"
                   style="font-weight: 500;">
                    <i class="fas fa-sign-in-alt me-1"></i>
                    <?= session('user_id') ? lang('backend_section') : lang('login') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
