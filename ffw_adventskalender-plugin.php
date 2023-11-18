<?php
/*
Plugin Name: FFW Adventskalender
Description: Ein einfacher Adventskalender für Ihre Website, speziell für Feuerwehren.
Version: 1.0
Author: Ihr Name
*/

// Registrieren der Styles und Scripts
function ffw_adventskalender_enqueue_scripts() {
    // Adventskalender JS
    wp_enqueue_script('ffw_adventskalender-frontend', plugin_dir_url(__FILE__) . 'public/js/adventskalender.js', array('bootstrap-js'), null, true);

       // Abrufen der Einstellungen
       $adventskalender_settings = array(
        'show_past_popups' => get_option('ffw_adventskalender_show_past_popups', true),
        'show_past_images' => get_option('ffw_adventskalender_show_past_images', true),
        'info_button_label' => get_option('ffw_adventskalender_info_button_label', 'Weitere Informationen'),
        'not_open_label' => get_option('ffw_adventskalender_not_open_label', 'Adventskalender'),
        'display_mode' => get_option('ffw_adventskalender_display_mode', 'default')
    );

    // Lokalisieren der Skripte für AJAX und Hintergrundbilder
    $backgroundImages = array(
        'large' => get_option('backgroundImageUrlLarge'),
        'small' => get_option('backgroundImageUrlSmall')
    );

   // Farbeinstellungen an das Skript übergeben
   $calendar_colors = array(
       'borderColor' => get_option('ffw_adventskalender_border_color', '#000000'),
       'textColor' => get_option('ffw_adventskalender_text_color', '#FFFFFF')
   );
    wp_localize_script('ffw_adventskalender-frontend', 'ffwAdventskalenderBackgroundImages', $backgroundImages);
    wp_localize_script('ffw_adventskalender-frontend', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_localize_script('ffw_adventskalender-frontend', 'ffwAdventskalenderAdditionalSettings', $adventskalender_settings);
    wp_localize_script('ffw_adventskalender-frontend', 'ffwAdventskalenderColors', $calendar_colors);


    // CSS-Stile
    wp_enqueue_style('ffw_adventskalender-style', plugin_dir_url(__FILE__) . 'public/css/adventskalender.css');
    
    // Bootstrap CSS
    wp_enqueue_style('bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');

    // Bootstrap JS (Popper.js ist in Bootstrap 5 integriert)
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js', array(), '5.3.2', true);
}

add_action('wp_enqueue_scripts', 'ffw_adventskalender_enqueue_scripts');


function add_bootstrap_css_attributes($html, $handle) {
    if ($handle === 'bootstrap-style') {
        return str_replace("rel='stylesheet'", "rel='stylesheet' integrity='sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN' crossorigin='anonymous'", $html);
    }
    return $html;
}
add_filter('style_loader_tag', 'add_bootstrap_css_attributes', 10, 2);

function add_bootstrap_js_attributes($tag, $handle) {
    if ($handle === 'bootstrap-js') {
        return str_replace(" src", " integrity='sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+' crossorigin='anonymous' src", $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'add_bootstrap_js_attributes', 10, 2);


// Einbinden der Backend-Logik
require_once plugin_dir_path(__FILE__) . 'includes/admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';

// Shortcode für den Adventskalender
function ffw_adventskalender_shortcode() {
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'public/partials/adventskalender-display.php';
    ffw_adventskalender_display_frontend();
    return ob_get_clean();
}

add_shortcode('ffw_adventskalender', 'ffw_adventskalender_shortcode');

function ffw_adventskalender_get_door_data() {
    $day = isset($_POST['day']) ? intval($_POST['day']) : 0;
    $options = get_option('ffw_adventskalender_options');
    $doorData = isset($options['day' . $day]) ? $options['day' . $day] : null;

    wp_send_json($doorData);
}

add_action('wp_ajax_get_door_data', 'ffw_adventskalender_get_door_data');
add_action('wp_ajax_nopriv_get_door_data', 'ffw_adventskalender_get_door_data');


?>
