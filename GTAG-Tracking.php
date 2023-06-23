<?php
/*
 * Plugin Name: GTAG Tracking
 * Plugin URI: https://github.com/alexmoise/GTAG-Tracking
 * GitHub Plugin URI: https://github.com/alexmoise/GTAG-Tracking
 * Description: A custom plugin that allows saving six pieces of tracking code and displaying them in <head> or after opening the <body>, conditionally to one, more, or all pages. Best used for GTAG tracking or anything similar (like pixels, validations etc.). Made using chat.openai.com (mostly). For details/troubleshooting please contact me at <a href="https://moise.pro/contact/">https://moise.pro/contact/</a>
 * Version: 0.1.0
 * Author: Alex Moise
 * Author URI: https://moise.pro
 * WC requires at least: 4.9.0
 * WC tested up to: 7.8.0
 */
*/

// Add the settings page
function gtag_tracking_settings_page() {
    add_menu_page(
        'GTAG Tracking',
        'GTAG Tracking',
        'manage_options',
        'gtag-tracking',
        'gtag_tracking_render_settings_page'
    );
}
add_action('admin_menu', 'gtag_tracking_settings_page');

// Render the settings page
function gtag_tracking_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>GTAG Tracking</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('gtag_tracking_options');
            do_settings_sections('gtag-tracking');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

// Register settings and sections
function gtag_tracking_register_settings() {
    add_settings_section(
        'gtag_tracking_section_global_site_tag',
        'Global Site Tag',
        'gtag_tracking_section_global_site_tag_callback',
        'gtag-tracking'
    );
    add_settings_section(
        'gtag_tracking_section_purchase_tracking_code',
        'Purchase Tracking Code',
        'gtag_tracking_section_purchase_tracking_code_callback',
        'gtag-tracking'
    );
    add_settings_section(
        'gtag_tracking_section_calls_from_website',
        'Calls from Website',
        'gtag_tracking_section_calls_from_website_callback',
        'gtag-tracking'
    );
    add_settings_section(
        'gtag_tracking_section_thank_you_conversion',
        'Thank You Page Conversion',
        'gtag_tracking_section_thank_you_conversion_callback',
        'gtag-tracking'
    );
    add_settings_section(
        'gtag_tracking_section_google_tag_manager',
        'Google Tag Manager',
        'gtag_tracking_section_google_tag_manager_callback',
        'gtag-tracking'
    );
    add_settings_section(
        'gtag_tracking_section_no_script_fallback',
        'No script fallback',
        'gtag_tracking_section_no_script_fallback_callback',
        'gtag-tracking'
    );

    // Register fields for each section
    register_setting('gtag_tracking_options', 'gfwgtag_global_site_tag');
    register_setting('gtag_tracking_options', 'gfwgtag_purchase_tracking_code');
    register_setting('gtag_tracking_options', 'gfwgtag_calls_from_website');
    register_setting('gtag_tracking_options', 'gfwgtag_thank_you_conversion');
    register_setting('gtag_tracking_options', 'gfwgtag_google_tag_manager');
    register_setting('gtag_tracking_options', 'gfwgtag_no_script_fallback');

    // Add fields to each section
    add_settings_field(
        'gtag_tracking_field_global_site_tag',
        'Global Site Tag Code',
        'gtag_tracking_field_global_site_tag_callback',
        'gtag-tracking',
        'gtag_tracking_section_global_site_tag'
    );
    add_settings_field(
        'gtag_tracking_field_purchase_tracking_code',
        'Purchase Tracking Code',
        'gtag_tracking_field_purchase_tracking_code_callback',
        'gtag-tracking',
        'gtag_tracking_section_purchase_tracking_code'
    );
    add_settings_field(
        'gtag_tracking_field_calls_from_website',
        'Calls from Website',
        'gtag_tracking_field_calls_from_website_callback',
        'gtag-tracking',
        'gtag_tracking_section_calls_from_website'
    );
    add_settings_field(
        'gtag_tracking_field_thank_you_conversion',
        'Thank You Page Conversion',
        'gtag_tracking_field_thank_you_conversion_callback',
        'gtag-tracking',
        'gtag_tracking_section_thank_you_conversion'
    );
    add_settings_field(
        'gtag_tracking_field_google_tag_manager',
        'Google Tag Manager',
        'gtag_tracking_field_google_tag_manager_callback',
        'gtag-tracking',
        'gtag_tracking_section_google_tag_manager'
    );
    add_settings_field(
        'gtag_tracking_field_no_script_fallback',
        'No script fallback',
        'gtag_tracking_field_no_script_fallback_callback',
        'gtag-tracking',
        'gtag_tracking_section_no_script_fallback'
    );
}
add_action('admin_init', 'gtag_tracking_register_settings');

// Field callbacks
function gtag_tracking_field_global_site_tag_callback() {
    $value = get_option('gfwgtag_global_site_tag');
    echo '<textarea name="gfwgtag_global_site_tag" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
}

function gtag_tracking_field_purchase_tracking_code_callback() {
    $value = get_option('gfwgtag_purchase_tracking_code');
    echo '<textarea name="gfwgtag_purchase_tracking_code" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
}

function gtag_tracking_field_calls_from_website_callback() {
    $value = get_option('gfwgtag_calls_from_website');
    echo '<textarea name="gfwgtag_calls_from_website" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
}

function gtag_tracking_field_thank_you_conversion_callback() {
    $value = get_option('gfwgtag_thank_you_conversion');
    echo '<textarea name="gfwgtag_thank_you_conversion" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
}

function gtag_tracking_field_google_tag_manager_callback() {
    $value = get_option('gfwgtag_google_tag_manager');
    echo '<textarea name="gfwgtag_google_tag_manager" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
}

function gtag_tracking_field_no_script_fallback_callback() {
    $value = get_option('gfwgtag_no_script_fallback');
    echo '<textarea name="gfwgtag_no_script_fallback" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
}

// Section callbacks
function gtag_tracking_section_global_site_tag_callback() {
    // Section content
}

function gtag_tracking_section_purchase_tracking_code_callback() {
    // Section content
}

function gtag_tracking_section_calls_from_website_callback() {
    // Section content
}

function gtag_tracking_section_thank_you_conversion_callback() {
    // Section content
}

function gtag_tracking_section_google_tag_manager_callback() {
    // Section content
}

function gtag_tracking_section_no_script_fallback_callback() {
    // Section content
}
?>
