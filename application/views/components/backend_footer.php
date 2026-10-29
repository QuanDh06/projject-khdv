<?php
/**
 * Local variables.
 *
 * @var string $user_display_name
 */
?>
<div id="footer" class="d-flex flex-wrap justify-content-between align-items-center px-4 py-2 mt-auto bg-body border-top shadow-sm" style="font-size: 13px;">
    <div class="d-flex align-items-center gap-3 my-1">
        <span id="select-language" class="badge rounded-pill bg-secondary px-3 py-2" style="cursor: pointer; font-weight: 500;">
            <i class="fas fa-globe me-1"></i>
            <?= ucfirst(config('language')) ?>
        </span>

        <a href="<?= site_url('appointments') ?>" class="text-decoration-none fw-medium d-inline-flex align-items-center">
            <i class="fas fa-calendar-check me-2 text-primary"></i>
            <?= lang('go_to_booking_page') ?>
        </a>
    </div>

    <div class="my-1 text-muted">
        <i class="fas fa-user-circle me-1 text-primary"></i>
        <strong id="footer-user-display-name" class="text-dark">
            <?= lang('hello') . ', ' . e($user_display_name) ?>!
        </strong>
    </div>
</div>
