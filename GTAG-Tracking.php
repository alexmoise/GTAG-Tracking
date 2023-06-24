<?php
/*
 * Plugin Name: GTAG Tracking
 * Plugin URI: https://github.com/alexmoise/GTAG-Tracking
 * GitHub Plugin URI: https://github.com/alexmoise/GTAG-Tracking
 * Description: A custom plugin that allows saving six pieces of tracking code and displaying them in head or after opening the body, conditionally to one, more, or all pages. Built for GTAG tracking but can accomodate anything similar (like pixels, validations etc.). Made using chat.openai.com (mostly). For details/troubleshooting please contact me at <a href="https://moise.pro/contact/">https://moise.pro/contact/</a>
 * Version: 1.0.5
 * Author: Alex Moise
 * Author URI: https://moise.pro
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

// Add a settings link on the Plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'gtag_tracking_plugin_settings_link');

function gtag_tracking_plugin_settings_link($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=gtag-tracking') . '">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

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

// Add a bit of CSS for te settings page
add_action('admin_head', 'gtag_tracking_settings_page_css');
function gtag_tracking_settings_page_css() {
	$current_screen = get_current_screen();
	if ($current_screen && $current_screen->id === 'toplevel_page_gtag-tracking') {
		echo '<style>';
		echo '
		.form-table td * {
		display: block;
		width: 500px;
		}
		.input-label {
		margin: 8px 0 5px 0;
		}
		.form-table tr > * {
		padding: 5px 0 0 0;
		}
		.wrap h2 {
		margin: 25px 0 0 0;
		}
		';
		echo '</style>';
	}
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
    $pages = is_array($pages) ? $pages : array();
    echo '<div class="input-label">Code:</div><textarea name="gfwgtag_global_site_tag" rows="10" >' . esc_textarea($value) . '</textarea>';
    echo '<div class="input-label">Position:</div><select name="gfwgtag_global_site_tag_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header (as high as possible)</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body (after tag opening)</option>';
    echo '</select>';
    echo '<div class="input-label">Location:</div><select multiple="multiple" name="gfwgtag_global_site_tag_pages[]">';
    echo '<option value="everywhere"' . selected(in_array('everywhere', $pages), true, false) . '>Everywhere</option>'; // Add the "everywhere" option
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
    $pages = is_array($pages) ? $pages : array();
    echo '<div class="input-label">Code:</div><textarea name="gfwgtag_purchase_tracking_code" rows="10">' . esc_textarea($value) . '</textarea>';
    echo '<div class="input-label">Position:</div><select name="gfwgtag_purchase_tracking_code_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header (as high as possible)</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body (after tag opening)</option>';
    echo '</select>';
    echo '<div class="input-label">Location:</div><select multiple="multiple" name="gfwgtag_purchase_tracking_code_pages[]">';
    echo '<option value="everywhere"' . selected(in_array('everywhere', $pages), true, false) . '>Everywhere</option>'; // Add the "everywhere" option
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
    $pages = is_array($pages) ? $pages : array();
    echo '<div class="input-label">Code:</div><textarea name="gfwgtag_calls_from_website" rows="10">' . esc_textarea($value) . '</textarea>';
    echo '<div class="input-label">Position:</div><select name="gfwgtag_calls_from_website_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header (as high as possible)</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body (after tag opening)</option>';
    echo '</select>';
    echo '<div class="input-label">Location:</div><select multiple="multiple" name="gfwgtag_calls_from_website_pages[]">';
    echo '<option value="everywhere"' . selected(in_array('everywhere', $pages), true, false) . '>Everywhere</option>'; // Add the "everywhere" option
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
    $pages = is_array($pages) ? $pages : array();
    echo '<div class="input-label">Code:</div><textarea name="gfwgtag_thank_you_conversion" rows="10">' . esc_textarea($value) . '</textarea>';
    echo '<div class="input-label">Position:</div><select name="gfwgtag_thank_you_conversion_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header (as high as possible)</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body (after tag opening)</option>';
    echo '</select>';
    echo '<div class="input-label">Location:</div><select multiple="multiple" name="gfwgtag_thank_you_conversion_pages[]">';
    echo '<option value="everywhere"' . selected(in_array('everywhere', $pages), true, false) . '>Everywhere</option>'; // Add the "everywhere" option
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
    $pages = is_array($pages) ? $pages : array();
    echo '<div class="input-label">Code:</div><textarea name="gfwgtag_google_tag_manager" rows="10">' . esc_textarea($value) . '</textarea>';
    echo '<div class="input-label">Position:</div><select name="gfwgtag_google_tag_manager_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header (as high as possible)</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body (after tag opening)</option>';
    echo '</select>';
    echo '<div class="input-label">Location:</div><select multiple="multiple" name="gfwgtag_google_tag_manager_pages[]">';
    echo '<option value="everywhere"' . selected(in_array('everywhere', $pages), true, false) . '>Everywhere</option>'; // Add the "everywhere" option
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
    $pages = is_array($pages) ? $pages : array();
    echo '<div class="input-label">Code:</div><textarea name="gfwgtag_no_script_fallback" rows="10">' . esc_textarea($value) . '</textarea>';
    echo '<div class="input-label">Position:</div><select name="gfwgtag_no_script_fallback_location">';
    echo '<option value="Header" ' . selected($location, 'Header', false) . '>Header (as high as possible)</option>';
    echo '<option value="Body" ' . selected($location, 'Body', false) . '>Body (after tag opening)</option>';
    echo '</select>';
    echo '<div class="input-label">Location:</div><select multiple="multiple" name="gfwgtag_no_script_fallback_pages[]">';
    echo '<option value="everywhere"' . selected(in_array('everywhere', $pages), true, false) . '>Everywhere</option>'; // Add the "everywhere" option
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

// === Now the output

// Conditionally output the scripts
add_action('template_redirect', 'gtag_tracking_output', 0);
function gtag_tracking_output() {
    $global_site_tag = get_option('gfwgtag_global_site_tag');
    $location = get_option('gfwgtag_global_site_tag_location');
    $selected_pages = get_option('gfwgtag_global_site_tag_pages');
	$selected_pages = is_array($selected_pages) ? $selected_pages : array();
	if (in_array('everywhere', $selected_pages) || in_array(get_queried_object_id(), $selected_pages)) {
		if (!empty($global_site_tag)) {
			if ($location === 'Header') {
				add_action('wp_head', function() use ($global_site_tag) { echo $global_site_tag; }, 0);
			} elseif ($location === 'Body') {
				add_action('wp_body_open', function() use ($global_site_tag) { echo $global_site_tag; }, 0);
			}
		}
	}
}

add_action('template_redirect', 'gtag_purchase_tracking_output', 0);
function gtag_purchase_tracking_output() {
    $purchase_tracking_code = get_option('gfwgtag_purchase_tracking_code');
    $location = get_option('gfwgtag_purchase_tracking_code_location');
    $selected_pages = get_option('gfwgtag_purchase_tracking_code_pages');
	$selected_pages = is_array($selected_pages) ? $selected_pages : array();
    if (in_array('everywhere', $selected_pages) || in_array(get_queried_object_id(), $selected_pages)) {
        if (!empty($purchase_tracking_code)) {
            if ($location === 'Header') {
				add_action('wp_head', function() use ($purchase_tracking_code) { echo $purchase_tracking_code; }, 0);
            } elseif ($location === 'Body') {
                add_action('wp_body_open', function() use ($purchase_tracking_code) { echo $purchase_tracking_code; }, 0);
            }
        }
    }
}

add_action('template_redirect', 'gtag_calls_from_website_output', 0);
function gtag_calls_from_website_output() {
    $calls_from_website = get_option('gfwgtag_calls_from_website');
    $location = get_option('gfwgtag_calls_from_website_location');
    $selected_pages = get_option('gfwgtag_calls_from_website_pages');
	$selected_pages = is_array($selected_pages) ? $selected_pages : array();
    if (in_array('everywhere', $selected_pages) || in_array(get_queried_object_id(), $selected_pages)) {
        if (!empty($calls_from_website)) {
            if ($location === 'Header') {
				add_action('wp_head', function() use ($calls_from_website) { echo $calls_from_website; }, 0);
            } elseif ($location === 'Body') {
                add_action('wp_body_open', function() use ($calls_from_website) { echo $calls_from_website; }, 0);
            }
        }
    }
}

add_action('template_redirect', 'gtag_thank_you_page_conversion_output', 0);
function gtag_thank_you_page_conversion_output() {
    $thank_you_conversion = get_option('gfwgtag_thank_you_conversion');
    $location = get_option('gfwgtag_thank_you_conversion_location');
    $selected_pages = get_option('gfwgtag_thank_you_conversion_pages');
	$selected_pages = is_array($selected_pages) ? $selected_pages : array();
    if (in_array('everywhere', $selected_pages) || in_array(get_queried_object_id(), $selected_pages)) {
        if (!empty($thank_you_conversion)) {
            if ($location === 'Header') {
				add_action('wp_head', function() use ($thank_you_conversion) { echo $thank_you_conversion; }, 0);
            } elseif ($location === 'Body') {
                add_action('wp_body_open', function() use ($thank_you_conversion) { echo $thank_you_conversion; }, 0);
            }
        }
    }
}

add_action('template_redirect', 'gtag_google_tag_manager_output', 0);
function gtag_google_tag_manager_output() {
    $google_tag_manager = get_option('gfwgtag_google_tag_manager');
    $location = get_option('gfwgtag_google_tag_manager_location');
    $selected_pages = get_option('gfwgtag_google_tag_manager_pages');
	$selected_pages = is_array($selected_pages) ? $selected_pages : array();
    if (in_array('everywhere', $selected_pages) || in_array(get_queried_object_id(), $selected_pages)) {
        if (!empty($google_tag_manager)) {
            if ($location === 'Header') {
				add_action('wp_head', function() use ($google_tag_manager) { echo $google_tag_manager; }, 0);
            } elseif ($location === 'Body') {
                add_action('wp_body_open', function() use ($google_tag_manager) { echo $google_tag_manager; }, 0);
            }
        }
    }
}

add_action('template_redirect', 'gtag_no_script_fallback_output', 0);
function gtag_no_script_fallback_output() {
    $no_script_fallback = get_option('gfwgtag_no_script_fallback');
    $location = get_option('gfwgtag_no_script_fallback_location');
    $selected_pages = get_option('gfwgtag_no_script_fallback_pages');
	$selected_pages = is_array($selected_pages) ? $selected_pages : array();
    if (in_array('everywhere', $selected_pages) || in_array(get_queried_object_id(), $selected_pages)) {
        if (!empty($no_script_fallback)) {
            if ($location === 'Header') {
				add_action('wp_head', function() use ($no_script_fallback) { echo $no_script_fallback; }, 0);
            } elseif ($location === 'Body') {
                add_action('wp_body_open', function() use ($no_script_fallback) { echo $no_script_fallback; }, 0);
            }
        }
    }
}

?>
