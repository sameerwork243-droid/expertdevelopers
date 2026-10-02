<?php
// pages/book-a-consultation.php — "Book a Consultation" landing page for the sales/property
// team. Uses the standard enquiry form (kind=contact, property_ref="Book a Consultation") so
// submissions land in the admin `inquiries` inbox like every other contact form.

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/store.php';
require_once __DIR__ . '/../includes/render/modules.php';
require_once __DIR__ . '/../includes/head.php';

$page_title = 'Book a Consultation';
$page_description = 'Speak with one of our Expert Marketing & Developers property consultants about buying, selling or investing in Pakistan real estate.';

$contact = contact_enquiry_form();
$contact = str_replace(
    '<form class="custom-form" data-enquiry-form="contact" novalidate>',
    '<form class="custom-form" data-enquiry-form="contact" novalidate>'
        . '<input type="hidden" name="property_ref" value="Book a Consultation" />',
    $contact
);

$body = '<div class="bv-page">'
    . '<div class="bv-banner"><div class="bv-banner-inner container">'
    . '<nav class="breadcrumbs bv-breadcrumbs" aria-label="breadcrumb"><ol class="breadcrumb">'
    . '<li class="breadcrumb-item enable-link-home"><a class="breadcrumb-link enable-link" href="/">Home</a></li>'
    . '<li class="breadcrumb-item active"><a aria-current="page" class="breadcrumb-link disable-link" href="/book-a-consultation/">Book a Consultation</a></li>'
    . '</ol></nav>'
    . '<h1 class="bv-title">Book a Consultation</h1>'
    . '<p class="bv-desc">' . esc($page_description) . '</p>'
    . '</div></div>'
    . '<div class="bv-body container"><div class="bv-grid">'
    . '<div class="bv-form-col"><div class="bv-card">'
    . '<div class="contact-form-wrapper"><div class="contact-form-container container">'
    . '<div class="content-section"><h3 class="title">Request your free consultation</h3>'
    . '<div class="description"><div><p>Tell us a little about your property goals and our consultants will get back to you within one business day.</p></div></div>'
    . '</div>'
    . '<div class="form-section">' . $contact . '</div>'
    . '</div></div>'
    . '</div></div>'
    . '<div class="bv-preview-col"><div class="bv-preview bv-preview-empty">'
    . '<p class="bv-preview-note">Our team covers buying, selling, renting, off-plan investments and property management in Pakistan.</p>'
    . '<a class="button button-orange" href="/property-services/"><span>Explore Services</span></a>'
    . '</div></div>'
    . '</div></div></div>';
?><!DOCTYPE html>
<html lang="en">
<head><?php render_head(); ?></head>
<body>
<?php require __DIR__ . '/../includes/header.php'; ?>
<main>
<?php echo $body; ?>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php render_site_footer_scripts(); ?>
</body>
</html>
