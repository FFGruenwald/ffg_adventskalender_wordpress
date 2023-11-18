<?php
function ffw_adventskalender_add_admin_menu() {
    // Erstellen des Menüeintrags
    $page_title = 'FFW Adventskalender Einstellungen';
    $menu_title = 'FFW Adventskalender';
    $capability = 'manage_options';
    $menu_slug  = 'ffw_adventskalender';
    $function   = 'ffw_adventskalender_options_page';
    $icon_url   = '';
    $position   = 65;

    $hook = add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);

    // Hinzufügen eines Aktion-Hooks, um das Skript nur auf dieser Seite zu laden
    add_action("load-{$hook}", 'ffw_adventskalender_load_admin_js');
}

// Funktion zum Laden des JavaScripts
function ffw_adventskalender_load_admin_js() {
    wp_enqueue_media();
    $script_path =  plugin_dir_url(dirname(__FILE__)) . 'public/js/admin.js';
    $script_version = filemtime($script_path); // Timestamp der letzten Dateiänderung
    wp_enqueue_script('ffw-adventskalender-admin', plugin_dir_url(dirname(__FILE__)) . 'public/js/admin.js', array('jquery'), $script_version, true);
}

add_action('admin_menu', 'ffw_adventskalender_add_admin_menu');

function ffw_adventskalender_options_page() {
    ?>
    <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <h2><?php echo esc_html(__('Hintergrundbilder', 'ffw_adventskalender')); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php echo esc_html(__('Hintergrundbild für große Bildschirme', 'ffw_adventskalender')); ?></th>
                    <td><?php ffw_adventskalender_image_field('backgroundImageUrlLarge'); ?></td>
                    <th scope="row"><?php echo esc_html(__('Hintergrundbild für kleine Bildschirme', 'ffw_adventskalender')); ?></th>
                    <td><?php ffw_adventskalender_image_field('backgroundImageUrlSmall'); ?></td>
                </tr>
            </table>

            <h2><?php echo esc_html(__('Weitere Einstellungen', 'ffw_adventskalender')); ?></h2>
            <table class="form-table">
            <tr>
    <th scope="row"><?php _e('Rahmenfarbe der Türchen', 'ffw_adventskalender'); ?></th>
    <td>
        <input type="text" name="ffw_adventskalender_border_color" class="ffw-color-picker" value="<?php echo esc_attr(get_option('ffw_adventskalender_border_color')); ?>" />
    </td>
</tr>
<tr>
    <th scope="row"><?php _e('Schriftfarbe der Zahlen', 'ffw_adventskalender'); ?></th>
    <td>
        <input type="text" name="ffw_adventskalender_text_color" class="ffw-color-picker" value="<?php echo esc_attr(get_option('ffw_adventskalender_text_color')); ?>" />
    </td>
</tr>
            <?php $display_mode = get_option('ffw_adventskalender_display_mode', 'default'); ?>
            <tr>
                <th scope="row"><?php echo esc_html(__('Kalendertürchen-Anzeige', 'ffw_adventskalender')); ?></th>
                <td>
                    <select id="ffw_adventskalender_display_mode" name="ffw_adventskalender_display_mode">
                    <option value="default"<?php echo ($display_mode == 'default' ? ' selected' : ''); ?>><?php echo esc_html(__('Standard Anordnung', 'ffw_adventskalender')); ?></option>
                    <option value="shuffle"<?php echo ($display_mode == 'shuffle' ? ' selected' : ''); ?>><?php echo esc_html(__('Zufällige Anordnung', 'ffw_adventskalender')); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html(__('Label Button für weitere Informationen', 'ffw_adventskalender')); ?></th>
                <td>
                    <input type="text" id="ffw_adventskalender_info_button_label" name="ffw_adventskalender_info_button_label" value="<?php echo esc_attr(get_option('ffw_adventskalender_info_button_label', 'Weitere Informationen')); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html(__('Überschrift Popup Tür kann noch nicht geöffnet werden', 'ffw_adventskalender')); ?></th>
                <td>
                    <input type="text" id="ffw_adventskalender_not_open_label" name="ffw_adventskalender_not_open_label" value="<?php echo esc_attr(get_option('ffw_adventskalender_not_open_label', 'Adventskalender')); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html(__('Anzeige Popup vergangener Türchen', 'ffw_adventskalender')); ?></th>
                <td>
                    <input type="checkbox" id="ffw_adventskalender_show_past_popups" name="ffw_adventskalender_show_past_popups" value="1" <?php echo (get_option('ffw_adventskalender_show_past_popups', true) ? ' checked' : ''); ?>>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo esc_html(__('Bilderanzeige vergangener Türchen', 'ffw_adventskalender')); ?></th>
                <td>
                    <input type="checkbox" id="ffw_adventskalender_show_past_images" name="ffw_adventskalender_show_past_images" value="1" <?php echo (get_option('ffw_adventskalender_show_past_images', true) ? ' checked' : ''); ?>>
                </td>
            </tr>
            </table>

            <?php settings_fields('ffw_adventskalender_options_group'); ?>
            <?php do_settings_sections('ffw_adventskalender'); ?>
            <?php submit_button(__('Speichern', 'ffw_adventskalender')); ?>

        </form>
        <form method="post">
        <input type="hidden" name="action" value="delete_ffw_adventskalender_settings">
        <?php wp_nonce_field('delete_ffw_adventskalender_settings_action', 'delete_ffw_adventskalender_settings_nonce'); ?>
        <input type="submit" class="button delete-settings-button" value="<?php echo esc_attr(__('Alle Kalendereinstellungen löschen', 'ffw_adventskalender')); ?>" onclick="return confirm('<?php echo esc_js(__('Sind Sie sicher, dass Sie alle Einstellungen löschen möchten?', 'ffw_adventskalender')); ?>');">
         </form>
    </div>
    <?php
}

function ffw_adventskalender_handle_post_request() {
    if (isset($_POST['action']) && $_POST['action'] == 'delete_ffw_adventskalender_settings') {
        check_admin_referer('delete_ffw_adventskalender_settings_action', 'delete_ffw_adventskalender_settings_nonce');

        // Einstellungen löschen
        delete_option('backgroundImageUrlLarge');
        delete_option('backgroundImageUrlSmall');
        delete_option('ffw_adventskalender_border_color');
        delete_option('ffw_adventskalender_text_color');
        delete_option('ffw_adventskalender_options');
        add_action('admin_notices', 'ffw_adventskalender_admin_notice_settings_deleted');
    }
}

function ffw_adventskalender_admin_notice_settings_deleted() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e('Alle Einstellungen des Adventskalenders wurden gelöscht!', 'ffw_adventskalender'); ?></p>
    </div>
    <?php
}

add_action('admin_init', 'ffw_adventskalender_handle_post_request');


function ffw_adventskalender_admin_styles() {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('ffw-adventskalender-color-picker', plugin_dir_url(__FILE__) . '../public/js/color-picker.js', array('wp-color-picker'), false, true);
    ?>
    <style type="text/css">
        .form-table > tbody > tr:nth-child(even) {
            background-color: #ffffff;
        }
        .delete-settings-button {
            background-color: #DA1F3D !important;
            color: white !important;
            border-color: black !important;
        }
        .delete-settings-button:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .wrap form:last-child {
            text-align: right;
            padding-top: 20px;
        }
    </style>
    <?php
}
add_action('admin_enqueue_scripts', 'ffw_adventskalender_admin_styles');

function ffw_adventskalender_settings_init() {
    wp_enqueue_media();
    wp_enqueue_script('ffw-adventskalender-admin', plugin_dir_url(__FILE__) . '../public/js/admin.js', array('jquery'), null, true);

    register_setting('ffw_adventskalender_options_group', 'backgroundImageUrlLarge');
    register_setting('ffw_adventskalender_options_group', 'backgroundImageUrlSmall');
    register_setting('ffw_adventskalender_options_group', 'ffw_adventskalender_options');
    register_setting('ffw_adventskalender_options_group', 'ffw_adventskalender_display_mode');
    register_setting('ffw_adventskalender_options_group', 'ffw_adventskalender_info_button_label');
    register_setting('ffw_adventskalender_options_group', 'ffw_adventskalender_not_open_label');
    register_setting('ffw_adventskalender_options_group', 'ffw_adventskalender_show_past_popups');
    register_setting('ffw_adventskalender_options_group', 'ffw_adventskalender_show_past_images');
    register_setting('ffw_adventskalender_options_group', 'ffw_adventskalender_border_color');
    register_setting('ffw_adventskalender_options_group', 'ffw_adventskalender_text_color');

    add_settings_section(
        'ffw_adventskalender_settings_section', 
        __('Einstellungen für die einzelnen Türchen', 'ffw_adventskalender'), 
        'ffw_adventskalender_settings_section_cb', 
        'ffw_adventskalender'
    );
    
    add_action('admin_head', 'ffw_adventskalender_admin_styles');

    for ($i = 1; $i <= 24; $i++) {
        add_settings_field(
            'day' . $i,
            __('Türchen', 'ffw_adventskalender') . ' ' . $i,
            'ffw_adventskalender_render_settings_field',
            'ffw_adventskalender',
            'ffw_adventskalender_settings_section',
            array(
                'id' => sprintf('day%s', $i),
                'option_name' => 'ffw_adventskalender_options'
            )
        );
    }
}

function ffw_adventskalender_image_field($optionName) {
    $option = get_option($optionName);
    ?>
    <!-- Vorschaubild -->
    <img id="image-<?php echo $optionName; ?>" alt="" src="<?php echo esc_url($option); ?>" style="<?php echo (!empty($option) ? 'max-height: 100px; display: block;' : 'display: none;') ?>">

    <!-- Verstecktes Feld für die Bild-URL -->
    <input type="hidden" id="image-url-<?php echo $optionName; ?>" name="<?php echo $optionName; ?>" value="<?php echo esc_url($option); ?>">

    <!-- Button zum Öffnen der Medienbibliothek -->
    <button class="button select-image-button" data-target="#image-<?php echo $optionName; ?>"><?php echo __('Bild auswählen', 'ffw_adventskalender'); ?></button>
<?php
}

function ffw_adventskalender_settings_section_cb() {
    echo '<p>' . _e('Auswahl der einzelnen Bilder, Texte und Links für jeden Tag des Adventskalenders.', 'ffw_adventskalender') .'</p>';
}

function ffw_adventskalender_render_settings_field($args) {
    $options = get_option($args['option_name']);
    $day = $args['id'];

    // Stellen Sie sicher, dass $options ein Array ist
    if (!is_array($options)) {
        $options = array();
    }

    // Initialisieren Sie die Werte für das aktuelle Türchen, wenn nicht vorhanden
    if (!isset($options[$day])) {
        $options[$day] = array('image' => '', 'text' => '', 'link' => '');
    }

    // Werte für das aktuelle Türchen
    $image = isset($options[$day]['image']) ? $options[$day]['image'] : '';
    $text = isset($options[$day]['text']) ? $options[$day]['text'] : '';
    $link = isset($options[$day]['link']) ? $options[$day]['link'] : '';

    ?>
    <table class="form-table">
        <tr>
            <td rowspan="2" style="text-align: center; vertical-align: middle;">
                <img id="image-<?php echo $day; ?>" alt=""  src="<?php echo esc_url($image); ?>" style="<?php echo (!empty($image) ? 'max-height: 100px; display: block;' : 'display: none;') ?>">
                <br/>
                <button class="button select-image-button" id="image-button-<?php echo $day; ?>" data-target="#image-<?php echo $day; ?>"><?php echo _e('Bild auswählen', 'ffw_adventskalender'); ?></button>
                <input type="hidden" id="image-url-<?php echo $day; ?>" name="<?php echo $args['option_name'] . "[$day][image]"; ?>" value="<?php echo esc_url($image); ?>">
            </td>
            <td>
                <label for="text-<?php echo $day; ?>"><?php echo __('Bildbeschreibung:', 'ffw_adventskalender'); ?></label><br/>
                <textarea class="large-text" id="text-<?php echo $day; ?>" name="<?php echo $args['option_name'] . "[$day][text]"; ?>" rows="3"><?php echo esc_textarea($text); ?></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <label for="link-<?php echo $day; ?>"><?php echo __('Link:', 'ffw_adventskalender'); ?></label><br/>
                <input type="url" class="regular-text" id="link-<?php echo $day; ?>" name="<?php echo $args['option_name'] . "[$day][link]"; ?>" value="<?php echo esc_url($link); ?>">
            </td>
        </tr>
    </table>
    <?php
}


add_action('admin_menu', 'ffw_adventskalender_add_admin_menu');
add_action('admin_init', 'ffw_adventskalender_settings_init');

