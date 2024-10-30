<?php
/*
Plugin Name: CSS to Footer
Description: Loading CSS in the footer.
Version: 1.1
Author: Zakharov Yegor, Shekhtman Maxim
*/

//Initialising settings array
$csstofooter_options_arr = array(
    //String from user's input
    'csstofooter_input_string' => '',
    //Array of css ids in format we need to working with
    'csstofooter_css_arr' => ''
);
add_option( 'csstofooter_options', $csstofooter_options_arr);

//Adding plugin page to WordPress settings
add_action( 'admin_menu', 'csstofooter_create_settings_submenu' );
function csstofooter_create_settings_submenu() {
    add_options_page( 'CSS to Footer', 'CSStoFooter Settings',
        'manage_options', 'csstoofooter_settings_menu', 'csstofooter_settings_page' );
    add_action('admin_init', 'csstofooter_register_settings');
}
//Registering our settings
function csstofooter_register_settings() {
    register_setting( 'csstofooter-settings-group', 'csstofooter_options', 'csstofooter_sanitize_options' );
}

//Doing some actions after user's input
function csstofooter_sanitize_options( $input ) {
    //Just cleaning input
    $input['csstofooter_input_string'] = sanitize_text_field( $input['csstofooter_input_string'] );

    //Format and saving our array of CSS ids
    $input['csstofooter_css_arr'] = preg_split("/[,;]/",$input['csstofooter_input_string']);
    for ($i=0;$i<sizeof($input['csstofooter_css_arr']);$i++) {
        if (substr($input['csstofooter_css_arr'][$i], strlen($input['csstofooter_css_arr'][$i]) - strlen('-css')) == '-css') {
            $input['csstofooter_css_arr'][$i] = substr($input['csstofooter_css_arr'][$i], 0, -4);
        }
    }

    return $input;
}

//Simple form for settings page
function csstofooter_settings_page()
{ ?>
    <div class="wrap">
        <h2>CSS to Footer Plugin Options</h2>
        <form method="post" action="options.php">
        <?php settings_fields('csstofooter-settings-group'); ?>
        <?php $csstofooter_options = get_option('csstofooter_options'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">CSS IDs<br>(split them using "," or ";")</th>
                <td><input type="text" name="csstofooter_options[csstofooter_input_string]"
                           value="<?php echo esc_attr($csstofooter_options['csstofooter_input_string']); ?>"/>
                </td>
            </tr>
        </table>
        <p class="submit"><input type="submit" class="button-primary" value="Save Changes"/>
        </p>
        </form>
    </div>
    <?php
}
?>
<?php
//Taking off our css from the header
add_action('wp_print_styles','csstofooter_deregister_styles');
function csstofooter_deregister_styles() {
    $csstofooter_options_arr = get_option('csstofooter_options');

    if (!empty($csstofooter_options_arr['csstofooter_css_arr'])) {
        foreach ($csstofooter_options_arr['csstofooter_css_arr'] as $css) {
            wp_dequeue_style($css);
        }
    }
}

//Adding our css to the footer
add_action('wp_footer', 'csstofooter_register_styles');
function csstofooter_register_styles() {
    $csstofooter_options_arr = get_option('csstofooter_options');

    if (!empty($csstofooter_options_arr['csstofooter_css_arr'])) {
        foreach ($csstofooter_options_arr['csstofooter_css_arr'] as $css) {
            wp_enqueue_style($css);
        }
    }
}?>
