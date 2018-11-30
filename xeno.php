<?php
/**
 *  Plugin Name:        Xeno
 *  Plugin URI:         https://xenoapp.com/wordpress
 *  Description:        Your independent customer chat tool. Make your contacts feel special with the power of live response.
 *  Version:            1.0
 *  Author:             Xeno Team
 *  Author URI:         https://xenoapp.com/team
 *  License:            GPL2
 *  License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 *  GitHub Plugin URI:  https://github.com/xenoapp/xeno-for-wordpress
 *  GitHub Branch:      master
 **/

if (!defined('ABSPATH')) {
    die('You can not access this file.');
    exit;
}

class Xeno {
    const  plugin_folder_name = 'xeno';

    var $options = array();
    var $db_version = 1;
    function __construct() {
        add_action( 'wp_head', array( $this, 'wp_head' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'load_wp_scripts' ) );

        $this->option_defaults = array(
            'api_key' => '',
            'db_version' => $this->db_version,
        );
    }

    function load_wp_scripts(){
        $this->options = wp_parse_args(get_option('xeno_options'), $this->option_defaults);

        if (isset($this->options['api_key'])) {
            $this->options['api_key'] = esc_attr($this->options['api_key']);
            wp_enqueue_script('chat_script', 'https://cdn.xeno.app/chat.js');
            wp_enqueue_script('chat_script_init', plugins_url('/' . self::plugin_folder_name . '/xeno_init_script.php') . '?api_key=' . $this->options['api_key']);
        }
    }

    function wp_head() {
        //
    }

    function admin_init() {
        $this->options = wp_parse_args(get_option('xeno_options'), $this->option_defaults );
        $this->register_settings();
    }
    function admin_menu() {
        add_management_page(__('Xeno'), __('Xeno'), 'manage_options', 'xeno-settings', array($this, 'xeno_settings'));
    }
    function register_settings() {
        register_setting('xeno', 'xeno_options', array($this, 'xeno_sanitize'));
        add_settings_section('xeno_settings_section', 'Xeno Settings', array($this, 'xeno_settings_callback'), 'xeno-settings');
        add_settings_field('api_key', 'Widget Key', array($this, 'widget_id_callback'), 'xeno-settings', 'xeno_settings_section');
    }
    function xeno_settings_callback() {
        ?>
        <b>Your Widget ID is available on your widget page on <a target="_blank" href="https://xeno.app">xeno.app</a></b>
        <?php
    }
    function widget_id_callback() {
        ?>
        <input type="input" id="xeno_options[api_key]" name="xeno_options[api_key]" value="<?php echo ($this->options['api_key']); ?>" >
        <label for="xeno_options[api_key]"><?php _e('Paste your Widget Key here', 'xeno'); ?></label>
        <?php
    }
    function xeno_settings() {
        ?>
        <div class="wrap">
            <h2><?php _e('Xeno', 'xeno'); ?></h2>
            <form action="options.php" method="POST" >
                <?php
                settings_fields('xeno');
                do_settings_sections('xeno-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    function xeno_sanitize($input) {
        $options              = $this->options;
        $input['db_version']  = $this->db_version;
        foreach ($options as $key=>$value) {
            $output[$key] = sanitize_text_field($input[$key]);
        }
        return $output;
    }
    function add_settings_link($links, $file) {
        if (plugin_basename( __FILE__ ) == $file) {
            $settings_link = '<a href="' . admin_url('tools.php?page=xeno-settings') .'">' . __('Settings', 'xeno') . '</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }
}
new Xeno();
