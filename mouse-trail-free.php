<?php
/*
Plugin Name: Mouse Trail Free
Description: A free version of the mouse trail effect with limited customizable options.
Version: 1.4
Author: Fernando Miranda
Author URI: https://rott515.com/product-category/wordpress-plugins/
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Enqueue scripts and styles
function mtfree_enqueue_scripts() {
    // Enqueue on all pages
    wp_enqueue_style('mtfree_mouse_trail_style', plugin_dir_url(__FILE__) . 'style-free.css', array(), '1.4');
    wp_enqueue_script('mtfree_mouse_trail_script', plugin_dir_url(__FILE__) . 'trail-free.js', array('jquery'), '1.4', true);
    wp_script_add_data('mtfree_mouse_trail_script', 'defer', true);

    wp_localize_script('mtfree_mouse_trail_script', 'mtfree_params', array(
        'trail_color' => get_option('mtfree_mouse_trail_color', '#ffffff'),
        'trail_thickness' => '5',
        'trail_opacity' => '1',
        'trail_length' => '70',
        'trail_speed' => '0.4',
        'trail_enabled' => get_option('mtfree_mouse_trail_enabled', '1'),
        'trail_gradient_enabled' => '0',
        'trail_gradient_start' => '#ffffff',
        'trail_gradient_end' => '#000000',
    ));
}
add_action('wp_enqueue_scripts', 'mtfree_enqueue_scripts');

// Add the SVG for mouse trail
function mtfree_add_mouse_trail_svg() {
    ?>
    <svg class="trail" viewBox="0 0 1 1">
        <defs>
            <linearGradient id="trail-gradient" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#ffffff" />
                <stop offset="100%" stop-color="#000000" />
            </linearGradient>
        </defs>
        <path d="" />
    </svg>
    <?php
}
add_action('wp_footer', 'mtfree_add_mouse_trail_svg');

// Add options page to the admin menu
function mtfree_add_options_page() {
    add_options_page(
        'Mouse Trail Options',
        'Mouse Trail',
        'manage_options',
        'mtfree_mouse_trail_options',
        'mtfree_render_options_page'
    );
}
add_action('admin_menu', 'mtfree_add_options_page');

// Register and initialize settings
function mtfree_register_settings() {
    register_setting('mtfree_mouse_trail_options_group', 'mtfree_mouse_trail_color', 'sanitize_hex_color');
    register_setting('mtfree_mouse_trail_options_group', 'mtfree_mouse_trail_enabled', 'sanitize_text_field');
}
add_action('admin_init', 'mtfree_register_settings');

// Reset settings to default
function mtfree_reset_settings() {
    update_option('mtfree_mouse_trail_color', '#ffffff');
    update_option('mtfree_mouse_trail_enabled', '1');
}

// Set default settings on activation
function mtfree_set_default_settings() {
    if (get_option('mtfree_mouse_trail_color') === false) {
        add_option('mtfree_mouse_trail_color', '#ffffff');
    }
    if (get_option('mtfree_mouse_trail_enabled') === false) {
        add_option('mtfree_mouse_trail_enabled', '1');
    }
}
register_activation_hook(__FILE__, 'mtfree_set_default_settings');

// Settings page content
function mtfree_render_options_page() {
    if (isset($_POST['reset_mouse_trail_settings'])) {
        if (check_admin_referer('reset_mouse_trail_settings_nonce', 'reset_mouse_trail_nonce')) {
            mtfree_reset_settings();
            echo '<div class="updated"><p>Settings reset to defaults.</p></div>';
        } else {
            echo '<div class="error"><p>Nonce verification failed for reset action.</p></div>';
        }
    }
    ?>
    <div class="wrap">
        <h1>Mouse Trail Options</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('mtfree_mouse_trail_options_group');
            do_settings_sections('mtfree_mouse_trail_options_group');
            wp_nonce_field('mtfree_mouse_trail_options_update_nonce', 'mtfree_mouse_trail_options_nonce');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Trail Color</th>
                    <td>
                        <input type="color" name="mtfree_mouse_trail_color" value="<?php echo esc_attr(get_option('mtfree_mouse_trail_color', '#ffffff')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Trail Thickness</th>
                    <td>
                        <input type="number" min="1" name="mtfree_mouse_trail_thickness" value="5" disabled />
                        <span>Premium feature</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Trail Opacity</th>
                    <td>
                        <input type="number" min="0" max="1" step="0.1" name="mtfree_mouse_trail_opacity" value="1" disabled />
                        <span>Premium feature</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Trail Length</th>
                    <td>
                        <input type="number" min="10" name="mtfree_mouse_trail_length" value="70" disabled />
                        <span>Premium feature</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Trail Speed</th>
                    <td>
                        <input type="number" min="0.1" max="1" step="0.1" name="mtfree_mouse_trail_speed" value="0.4" disabled />
                        <span>Premium feature</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Trail</th>
                    <td>
                        <input type="checkbox" name="mtfree_mouse_trail_enabled" value="1" <?php checked(get_option('mtfree_mouse_trail_enabled'), '1'); ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Gradient</th>
                    <td>
                        <input type="checkbox" name="mtfree_mouse_trail_gradient_enabled" value="1" disabled />
                        <span>Premium feature</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Gradient Start Color</th>
                    <td>
                        <input type="color" name="mtfree_mouse_trail_gradient_start" value="#ffffff" disabled />
                        <span>Premium feature</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Gradient End Color</th>
                    <td>
                        <input type="color" name="mtfree_mouse_trail_gradient_end" value="#000000" disabled />
                        <span>Premium feature</span>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <form method="post" onsubmit="return confirm('Are you sure you want to reset to default settings?');">
            <?php wp_nonce_field('reset_mouse_trail_settings_nonce', 'reset_mouse_trail_nonce'); ?>
            <input type="hidden" name="reset_mouse_trail_settings" value="1" />
            <?php submit_button('Reset to Defaults', 'secondary'); ?>
        </form>
        <p><a href="https://rott515.com/mouse-trail-premium" target="_blank">Upgrade to Premium</a></p>
    </div>
    <?php
}
?>
