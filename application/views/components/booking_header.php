<?php
/**
 * Local variables.
 *
 * @var string $company_name
 */
?>

<div id="header" class="overflow-hidden p-4 d-flex flex-column flex-lg-row align-items-center justify-content-between bg-primary">
    <div id="company-name" class="d-flex flex-column flex-md-row align-items-center text-center text-md-start text-white my-2 my-md-0 flex-grow-1" style="min-width: 0;">
        <div class="hotel-logo-wrap d-flex align-items-center justify-content-center me-md-3 mb-2 mb-md-0">
            <img src="<?= e(vars('company_logo') ?: base_url('assets/img/logo.png')) ?>" alt="logo" id="company-logo"
                 style="max-height: 50px; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.25));">
        </div>

        <div>
            <div class="hotel-stars text-uppercase fw-semibold mb-1" style="font-size: 11px; letter-spacing: 2px; color: #e5c378;">
                ★ ★ ★ ★ ★ &nbsp;Luxury Hotel &amp; Resort
            </div>
            <div class="hotel-brand-title fw-bold text-white" style="font-size: 22px; letter-spacing: 0.4px; line-height: 1.2;">
                <?= e($company_name) ?>
            </div>
            <div class="d-flex justify-content-center justify-content-md-start mt-1">
                <span class="display-booking-selection small fw-normal" style="color: rgba(255,255,255,0.8);">
                    <i class="fas fa-concierge-bell me-1" style="color: #e5c378;"></i>
                    <?= lang('service') ?> │ <?= lang('provider') ?>
                </span>
            </div>
        </div>
    </div>

    <div id="steps" class="d-flex align-items-center justify-content-center my-2 my-md-0 ms-lg-3">
        <div id="step-1" class="book-step active-step d-inline-flex align-items-center justify-content-center"
             data-tippy-content="<?= lang('service_and_provider') ?>">
            <strong>1</strong>
        </div>

        <div id="step-2" class="book-step d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('appointment_date_and_time') ?>">
            <strong>2</strong>
        </div>

        <div id="step-3" class="book-step d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('customer_information') ?>">
            <strong>3</strong>
        </div>

        <div id="step-4" class="book-step d-inline-flex align-items-center justify-content-center me-0" data-bs-toggle="tooltip"
             data-tippy-content="<?= lang('appointment_confirmation') ?>">
            <strong>4</strong>
        </div>
    </div>
</div>
