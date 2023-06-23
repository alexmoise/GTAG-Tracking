<?php
/*
 * Plugin Name: GTAG Tracking
 * Plugin URI: https://github.com/alexmoise/GTAG-Tracking
 * GitHub Plugin URI: https://github.com/alexmoise/GTAG-Tracking
 * Description: A custom plugin that allows saving six pieces of tracking code and displaying them in head or after opening the body, conditionally to one, more, or all pages. Best used for GTAG tracking or anything similar (like pixels, validations etc.). Made using chat.openai.com (mostly). For details/troubleshooting please contact me at <a href="https://moise.pro/contact/">https://moise.pro/contact/</a>
 * Version: 0.2.1
 * Author: Alex Moise
 * Author URI: https://moise.pro
 * WC requires at least: 4.9.0
 * WC tested up to: 7.8.0
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
    $sections = array(
        array(
            'id' => 'global_site_tag',
            'title' => 'Global Site Tag'
        ),
        array(
            'id' => 'purchase_tracking_code',
            'title' => 'Purchase Tracking Code'
        ),
        array(
            'id' => 'calls_from_website',
            'title' => 'Calls from Website'
        ),
        array(
            'id' => 'thank_you_conversion',
            'title' => 'Thank You Page Conversion'
        ),
        array(
            'id' => 'google_tag_manager',
            'title' => 'Google Tag Manager'
        ),
        array(
            'id' => 'no_script_fallback',
            'title' => 'No script fallback'
        )
    );

    foreach ($sections as $section) {
        $section_id = 'gtag_tracking_section_' . $section['id'];
        add_settings_section(
            $section_id,
            $section['title'],
            function () use ($section_id) {
                // Section content
            },
            'gtag-tracking'
        );

        register_setting('gtag_tracking_options', 'gfwgtag_' . $section['id']);
        register_setting('gtag_tracking_options', 'gfwgtag_' . $section['id'] . '_location');
        register_setting('gtag_tracking_options', 'gfwgtag_' . $section['id'] . '_pages');

        $field_callback = 'gtag_tracking_field_' . $section['id'] . '_callback';
        add_settings_field(
            'gtag_tracking_field_' . $section['id'],
            $section['title'],
            $field_callback,
            'gtag-tracking',
            $section_id
        );
    }
}
add_action('admin_init', 'gtag_tracking_register_settings');

// Field callbacks
function gtag_tracking_field_global_site_tag_callback() {
    $value = get_option('gfwgtag_global_site_tag');
    $location = get_option('gfwgtag_global_site_tag_location');
    $pages = get_option('gfwgtag_global_site_tag_pages');
    
    echo '<textarea name="gfwgtag_global_site_tag" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
    echo '<select name="gfwgtag_global_site_tag_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body</option>';
    echo '</select>';
    echo '<select multiple="multiple" name="gfwgtag_global_site_tag_pages[]">';
    $pages_list = get_pages();
    foreach ($pages_list as $page) {
        echo '<option value="' . $page->ID . '" ' . selected(in_array($page->ID, $pages), true, false) . '>' . $page->post_title . '</option>';
    }
    echo '</select>';
}

function gtag_tracking_field_purchase_tracking_code_callback() {
    $value = get_option('gfwgtag_purchase_tracking_code');
    $location = get_option('gfwgtag_purchase_tracking_code_location');
    $pages = get_option('gfwgtag_purchase_tracking_code_pages');
    
    echo '<textarea name="gfwgtag_purchase_tracking_code" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
    echo '<select name="gfwgtag_purchase_tracking_code_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body</option>';
    echo '</select>';
    echo '<select multiple="multiple" name="gfwgtag_purchase_tracking_code_pages[]">';
    $pages_list = get_pages();
    foreach ($pages_list as $page) {
        echo '<option value="' . $page->ID . '" ' . selected(in_array($page->ID, $pages), true, false) . '>' . $page->post_title . '</option>';
    }
    echo '</select>';
}

function gtag_tracking_field_calls_from_website_callback() {
    $value = get_option('gfwgtag_calls_from_website');
    $location = get_option('gfwgtag_calls_from_website_location');
    $pages = get_option('gfwgtag_calls_from_website_pages');
    
    echo '<textarea name="gfwgtag_calls_from_website" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
    echo '<select name="gfwgtag_calls_from_website_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body</option>';
    echo '</select>';
    echo '<select multiple="multiple" name="gfwgtag_calls_from_website_pages[]">';
    $pages_list = get_pages();
    foreach ($pages_list as $page) {
        echo '<option value="' . $page->ID . '" ' . selected(in_array($page->ID, $pages), true, false) . '>' . $page->post_title . '</option>';
    }
    echo '</select>';
}

function gtag_tracking_field_thank_you_conversion_callback() {
    $value = get_option('gfwgtag_thank_you_conversion');
    $location = get_option('gfwgtag_thank_you_conversion_location');
    $pages = get_option('gfwgtag_thank_you_conversion_pages');
    
    echo '<textarea name="gfwgtag_thank_you_conversion" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
    echo '<select name="gfwgtag_thank_you_conversion_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body</option>';
    echo '</select>';
    echo '<select multiple="multiple" name="gfwgtag_thank_you_conversion_pages[]">';
    $pages_list = get_pages();
    foreach ($pages_list as $page) {
        echo '<option value="' . $page->ID . '" ' . selected(in_array($page->ID, $pages), true, false) . '>' . $page->post_title . '</option>';
    }
    echo '</select>';
}

function gtag_tracking_field_google_tag_manager_callback() {
    $value = get_option('gfwgtag_google_tag_manager');
    $location = get_option('gfwgtag_google_tag_manager_location');
    $pages = get_option('gfwgtag_google_tag_manager_pages');
    
    echo '<textarea name="gfwgtag_google_tag_manager" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
    echo '<select name="gfwgtag_google_tag_manager_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body</option>';
    echo '</select>';
    echo '<select multiple="multiple" name="gfwgtag_google_tag_manager_pages[]">';
    $pages_list = get_pages();
    foreach ($pages_list as $page) {
        echo '<option value="' . $page->ID . '" ' . selected(in_array($page->ID, $pages), true, false) . '>' . $page->post_title . '</option>';
    }
    echo '</select>';
}

function gtag_tracking_field_no_script_fallback_callback() {
    $value = get_option('gfwgtag_no_script_fallback');
    $location = get_option('gfwgtag_no_script_fallback_location');
    $pages = get_option('gfwgtag_no_script_fallback_pages');
    
    echo '<textarea name="gfwgtag_no_script_fallback" rows="5" cols="50">' . esc_textarea($value) . '</textarea>';
    echo '<select name="gfwgtag_no_script_fallback_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body</option>';
    echo '</select>';
    echo '<select multiple="multiple" name="gfwgtag_no_script_fallback_pages[]">';
    $pages_list = get_pages();
    foreach ($pages_list as $page) {
        echo '<option value="' . $page->ID . '" ' . selected(in_array($page->ID, $pages), true, false) . '>' . $page->post_title . '</option>';
    }
    echo '</select>';
}


// Section callbacks
function gtag_tracking_section_global_site_tag_callback() {
    // Section content
}

// Repeat the above section callbacks for each section
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

// Custom sanitization function to allow GTAG tracking codes
function sanitize_gtag_tracking_code($input) {
    // Maybe adjust this later for sanitization
    /* 
	$allowed_html = array(
        'script' => array(),
    );
    return wp_kses($input, $allowed_html);
	*/ 
	return stripslashes($input);
}

// Save settings
function gtag_tracking_save_settings() {
    if (isset($_POST['submit'])) {
        $sections = array(
            'global_site_tag',
            'purchase_tracking_code',
            'calls_from_website',
            'thank_you_conversion',
            'google_tag_manager',
            'no_script_fallback'
        );
        
        foreach ($sections as $section) {
            $code = sanitize_gtag_tracking_code($_POST['gfwgtag_' . $section]);
            $location = sanitize_text_field($_POST['gfwgtag_' . $section . '_location']);
            $pages = isset($_POST['gfwgtag_' . $section . '_pages']) ? $_POST['gfwgtag_' . $section . '_pages'] : array();
            
            update_option('gfwgtag_' . $section, $code);
            update_option('gfwgtag_' . $section . '_location', $location);
            update_option('gfwgtag_' . $section . '_pages', $pages);
        }
    }
}
add_action('admin_post_save_settings', 'gtag_tracking_save_settings');

// Initialize the plugin
function gtag_tracking_init() {
    if (isset($_POST['submit'])) {
        gtag_tracking_save_settings();
        wp_redirect(admin_url('admin.php?page=gtag-tracking'));
        exit;
    }
}
add_action('admin_init', 'gtag_tracking_init');

?>
