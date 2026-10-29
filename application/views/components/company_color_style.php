<?php
/**
 * Company Color Style Component
 *
 * Overrides Bootstrap CSS variables with the company's custom primary color.
 * This allows the entire application to automatically adopt the custom branding.
 *
 * @var string $company_color
 */

if (!function_exists('hexToRgb')) {
    /**
     * Convert hex color to RGB components
     */
    function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return sprintf(
            '%d, %d, %d',
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        );
    }
}

if (!function_exists('adjustBrightness')) {
    /**
     * Adjust color brightness
     * @param string $hex Hex color code
     * @param int $percent Positive = lighter, negative = darker
     */
    function adjustBrightness(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return sprintf(
            '#%02x%02x%02x',
            max(0, min(255, $r + ($r * $percent) / 100)),
            max(0, min(255, $g + ($g * $percent) / 100)),
            max(0, min(255, $b + ($b * $percent) / 100)),
        );
    }
}

if (!empty($company_color) && $company_color !== DEFAULT_COMPANY_COLOR):
    // Generate color variations

    // Strip to hex charset so a malformed stored value cannot break out of the <style> block.


    $company_color = '#' . preg_replace('/[^0-9A-Fa-f]/', '', $company_color);

    $rgb = hexToRgb($company_color);
    $hover = adjustBrightness($company_color, 15);
    $active = adjustBrightness($company_color, -10);
    $dark = adjustBrightness($company_color, -25);
    $darker = adjustBrightness($company_color, -40);
    $subtle = adjustBrightness($company_color, 80);
    $border_subtle = adjustBrightness($company_color, 40);
    ?>
<style>
    :root,
    [data-bs-theme="light"],
    [data-bs-theme="dark"] {
        /* Bootstrap primary color overrides */
        --bs-primary: <?= $company_color ?>;
        --bs-primary-rgb: <?= $rgb ?>;
        --bs-primary-bg-subtle: <?= $subtle ?>;
        --bs-primary-bg-subtle-rgb: <?= hexToRgb($subtle) ?>;
        --bs-primary-border-subtle: <?= $border_subtle ?>;
        --bs-primary-text-emphasis: <?= $darker ?>;
        
        /* Link colors */
        --bs-link-color: <?= $company_color ?>;
        --bs-link-color-rgb: <?= $rgb ?>;
        --bs-link-hover-color: <?= $hover ?>;
        --bs-link-hover-color-rgb: <?= hexToRgb($hover) ?>;
        
        /* Focus ring */
        --bs-focus-ring-color: rgba(<?= $rgb ?>, 0.25);
        
        /* Custom app color shades */
        --ea-primary-hover: <?= $hover ?>;
        --ea-primary-active: <?= $active ?>;
        --ea-primary-dark: <?= $dark ?>;
        --ea-primary-darker: <?= $darker ?>;
        --ea-primary-subtle: <?= $subtle ?>;
    }

    /*
     * Button component overrides
     *
     * Only set the Bootstrap CSS variables here so the existing theme rules
     * for .btn-primary / .btn-outline-primary (gradient, border, box-shadow)
     * keep working without being overridden by the company color style.
     */
    .btn-primary {
        --bs-btn-bg: var(--bs-primary);
        --bs-btn-border-color: var(--bs-primary);
        --bs-btn-hover-bg: var(--ea-primary-hover);
        --bs-btn-hover-border-color: var(--ea-primary-hover);
        --bs-btn-active-bg: var(--ea-primary-active);
        --bs-btn-active-border-color: var(--ea-primary-active);
        --bs-btn-disabled-bg: var(--bs-primary);
        --bs-btn-disabled-border-color: var(--bs-primary);
        --bs-btn-focus-shadow-rgb: var(--bs-primary-rgb);
    }

    .btn-outline-primary {
        --bs-btn-color: var(--bs-primary);
        --bs-btn-border-color: var(--bs-primary);
        --bs-btn-hover-color: var(--bs-white, #fff);
        --bs-btn-hover-bg: var(--bs-primary);
        --bs-btn-hover-border-color: var(--bs-primary);
        --bs-btn-active-color: var(--bs-white, #fff);
        --bs-btn-active-bg: var(--ea-primary-active);
        --bs-btn-active-border-color: var(--ea-primary-active);
        --bs-btn-disabled-color: var(--bs-primary);
        --bs-btn-disabled-border-color: var(--bs-primary);
        --bs-btn-focus-shadow-rgb: var(--bs-primary-rgb);
    }


    /* Form component overrides */
    /*
    .form-control:focus,
    .form-select:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
    }

    .form-check-input:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .form-check-input:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
    }
    */

    /* Navigation component overrides */
    .nav-pills {
        --bs-nav-pills-link-active-bg: var(--bs-primary);
    }

    /*
    .nav-pills .nav-link {
        color: var(--bs-primary);
    }

    .nav-pills .nav-link:hover:not(.active) {
        background: rgba(var(--bs-primary-rgb), 0.08);
        color: var(--ea-primary-active);
    }

    .nav-pills .nav-link.active {
        background: var(--bs-primary);
        color: var(--bs-white, #fff);
    }

    .nav-pills .nav-link:focus {
        outline: none;
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
    }

    .nav-tabs .nav-link:hover {
        background: rgba(var(--bs-primary-rgb), 0.05);
        border-color: var(--bs-border-color) var(--bs-border-color) transparent;
    }

    .nav-tabs .nav-link.active {
        color: var(--bs-primary);
        border-color: var(--bs-border-color) var(--bs-border-color) var(--bs-white);
    }

    .nav-tabs .nav-link:focus {
        outline: none;
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
    }
    */

    .nav-link {
        --bs-nav-link-color: var(--bs-primary);
        --bs-nav-link-hover-color: var(--ea-primary-hover);
    }

    .dropdown-menu {
        --bs-dropdown-link-active-bg: var(--bs-primary);
    }

    /*
    .dropdown-menu .dropdown-item:hover,
    .dropdown-menu .dropdown-item:focus {
        background: rgba(var(--bs-primary-rgb), 0.08);
    }
    */

    .pagination {
        --bs-pagination-active-bg: var(--bs-primary);
        --bs-pagination-active-border-color: var(--bs-primary);
    }

    /* Alert component overrides */
    /*
    .alert-primary {
        background: rgba(var(--bs-primary-rgb), 0.1);
        color: var(--ea-primary-darker);
    }
    */

    /* Other component overrides */
    .progress {
        --bs-progress-bar-bg: var(--bs-primary);
    }

    .list-group {
        --bs-list-group-active-bg: var(--bs-primary);
        --bs-list-group-active-border-color: var(--bs-primary);
    }

    /*
    .list-group-item:hover {
        background: rgba(var(--bs-primary-rgb), 0.03);
    }
    */

    .accordion {
        --bs-accordion-active-color: var(--bs-primary);
        --bs-accordion-btn-focus-box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
    }

    /* Selection color */
    ::selection {
        background: rgba(var(--bs-primary-rgb), 0.2);
    }

    /* Body background gradient (kept on top of the theme background so dark themes stay dark) */
    body {
        background-color: var(--bs-body-bg);
        background-image: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.03) 0%, rgba(var(--bs-primary-rgb), 0.01) 100%);
    }

    /* Application-specific overrides */
    #header,
    #book-appointment-wizard #header,
    #frame-footer .backend-link {
        background-color: var(--bs-primary) !important;
        border-color: var(--bs-primary) !important;
    }

    #book-appointment-wizard .book-step {
        background: var(--ea-primary-dark);
    }

    #book-appointment-wizard .book-step strong {
        color: var(--ea-primary-subtle);
    }

    #book-appointment-wizard #available-hours .selected-hour {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    #book-appointment-wizard #company-name .display-selected-service,
    #book-appointment-wizard #company-name .display-selected-provider,
    #book-appointment-wizard #company-name .display-booking-selection {
        color: var(--ea-primary-darker);
        border-right-color: var(--ea-primary-darker);
    }

    .backend-page .filter-records .results .entry.selected {
        border-right-color: var(--bs-primary) !important;
    }

    #existing-customers-list div:hover {
        background: var(--bs-primary) !important;
    }
</style>
<?php
endif; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --hotel-navy: #0f2942;
        --hotel-navy-light: #1c4268;
        --hotel-gold: #c5a059;
        --hotel-gold-light: #e5c378;
        --hotel-cream: #fcfaf5;
        --bs-primary: #143656;
        --bs-primary-rgb: 20, 54, 86;
    }

    body {
        font-family: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        background: radial-gradient(circle at top left, #eef2f7 0%, #e3e9f2 50%, #dde4ef 100%) !important;
        color: #1e293b;
    }

    /* Booking Wizard Container */
    #book-appointment-wizard {
        border-radius: 20px !important;
        box-shadow: 0 22px 60px rgba(15, 41, 66, 0.16), 0 4px 16px rgba(15, 41, 66, 0.06) !important;
        border: 1px solid rgba(197, 160, 89, 0.28) !important;
        background: #ffffff !important;
    }

    /* Header Luxury Gradient & Gold Trim */
    #book-appointment-wizard #header,
    nav#header.navbar {
        background: linear-gradient(135deg, #0c2238 0%, #173a5e 55%, #0f2942 100%) !important;
        border-bottom: 3px solid var(--hotel-gold) !important;
    }

    .hotel-brand-title {
        font-family: 'Playfair Display', Georgia, serif;
    }

    /* Step Indicators */
    #book-appointment-wizard #steps {
        width: auto !important;
        gap: 10px;
    }

    #book-appointment-wizard .book-step {
        height: 38px !important;
        width: 38px !important;
        padding: 0 !important;
        margin: 0 !important;
        border-radius: 50% !important;
        background: rgba(255, 255, 255, 0.12) !important;
        border: 1.5px solid rgba(229, 195, 120, 0.45) !important;
        transition: all 0.25s ease !important;
    }

    #book-appointment-wizard .book-step strong {
        font-size: 14px !important;
        color: rgba(255, 255, 255, 0.75) !important;
        line-height: 1 !important;
    }

    #book-appointment-wizard .book-step.active-step {
        height: 46px !important;
        width: 46px !important;
        background: linear-gradient(135deg, #e5c378 0%, #c5a059 100%) !important;
        border-color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(197, 160, 89, 0.5) !important;
    }

    #book-appointment-wizard .book-step.active-step strong {
        font-size: 18px !important;
        color: #0c2238 !important;
        font-weight: 700 !important;
    }

    /* Step Titles */
    #book-appointment-wizard .frame-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-weight: 600 !important;
        color: var(--hotel-navy) !important;
        font-size: 1.75rem !important;
        position: relative;
        padding-bottom: 14px;
        margin-top: 1.2rem !important;
        margin-bottom: 2rem !important;
    }

    #book-appointment-wizard .frame-title::after {
        content: "";
        display: block;
        width: 64px;
        height: 3px;
        background: linear-gradient(90deg, var(--hotel-gold), var(--hotel-gold-light));
        margin: 10px auto 0;
        border-radius: 2px;
    }

    /* Form Controls & Selects */
    #book-appointment-wizard .form-select,
    #book-appointment-wizard .form-control,
    .card .form-control {
        border-radius: 12px !important;
        padding: 0.7rem 1rem !important;
        border: 1.5px solid #cbd5e1 !important;
        font-size: 0.96rem !important;
        background-color: #f8fafc;
        transition: all 0.2s ease;
    }

    #book-appointment-wizard .form-select:focus,
    #book-appointment-wizard .form-control:focus,
    .card .form-control:focus {
        background-color: #ffffff;
        border-color: var(--hotel-gold) !important;
        box-shadow: 0 0 0 4px rgba(197, 160, 89, 0.18) !important;
    }

    /* Service Description Card */
    #book-appointment-wizard #service-description {
        background: var(--hotel-cream) !important;
        border-left: 4px solid var(--hotel-gold) !important;
        border-top: 1px solid rgba(197, 160, 89, 0.25);
        border-right: 1px solid rgba(197, 160, 89, 0.25);
        border-bottom: 1px solid rgba(197, 160, 89, 0.25);
        border-radius: 12px !important;
        padding: 16px 20px !important;
        max-height: 220px !important;
        font-size: 0.94rem !important;
        color: #334155;
        box-shadow: 0 4px 12px rgba(15, 41, 66, 0.04) !important;
    }

    /* Available Hours Buttons */
    #book-appointment-wizard #available-hours .available-hour {
        border-radius: 10px !important;
        border: 1.5px solid #cbd5e1 !important;
        font-weight: 500 !important;
        padding: 8px 14px !important;
        margin-bottom: 8px !important;
        transition: all 0.2s ease !important;
        background: #ffffff;
        color: var(--hotel-navy);
    }

    #book-appointment-wizard #available-hours .available-hour:hover {
        border-color: var(--hotel-gold) !important;
        background: var(--hotel-cream) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(15, 41, 66, 0.08);
    }

    #book-appointment-wizard #available-hours .selected-hour {
        background: linear-gradient(135deg, #0f2942 0%, #1c4268 100%) !important;
        border-color: var(--hotel-gold) !important;
        color: #ffffff !important;
        box-shadow: 0 5px 14px rgba(15, 41, 66, 0.25) !important;
    }

    /* Wizard Next / Back / Confirm Buttons */
    #book-appointment-wizard .button-next,
    #book-appointment-wizard #book-appointment-submit,
    .btn-primary {
        background: linear-gradient(135deg, #0f2942 0%, #1c4268 100%) !important;
        border: 1px solid var(--hotel-gold) !important;
        color: #ffffff !important;
        border-radius: 50px !important;
        padding: 10px 26px !important;
        font-weight: 600 !important;
        letter-spacing: 0.3px;
        box-shadow: 0 6px 16px rgba(15, 41, 66, 0.2) !important;
        transition: all 0.25s ease !important;
    }

    #book-appointment-wizard .button-next:hover,
    #book-appointment-wizard #book-appointment-submit:hover,
    .btn-primary:hover {
        background: linear-gradient(135deg, #173a5e 0%, #245280 100%) !important;
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(15, 41, 66, 0.28) !important;
    }

    #book-appointment-wizard .button-back {
        border-radius: 50px !important;
        padding: 10px 24px !important;
        font-weight: 500 !important;
    }

    /* Flatpickr Calendar Polish */
    .flatpickr-calendar.inline {
        border-radius: 16px !important;
        box-shadow: 0 8px 24px rgba(15, 41, 66, 0.08) !important;
        border: 1px solid #e2e8f0 !important;
        overflow: hidden;
    }

    .flatpickr-months,
    .flatpickr-weekdays,
    span.flatpickr-weekday {
        background: var(--hotel-navy) !important;
        color: #ffffff !important;
    }

    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
        background: var(--hotel-gold) !important;
        border-color: var(--hotel-gold) !important;
        color: #0c2238 !important;
        font-weight: 700 !important;
    }

    /* Login & Confirmation Cards */
    .card,
    #message-frame {
        border-radius: 20px !important;
        border: 1px solid rgba(197, 160, 89, 0.28) !important;
        box-shadow: 0 20px 50px rgba(15, 41, 66, 0.14) !important;
    }
</style>
