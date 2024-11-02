<?php
/* 
Plugin Name: LiteShamsi
Description: This plugin allows you to easily convert the core WordPress dates to Jalali (Shamsi).
Version: 1.3.2
Author: motavakel
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: mo-jalali-calendar
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit;
}

define('DATEKIT_DIR', plugin_dir_path(__FILE__));
define('DATEKIT_URL', plugin_dir_url(__FILE__));
define('DATEKIT_FUNC', DATEKIT_DIR . 'admin');
define('DATEKIT_ASSETS', DATEKIT_URL . 'assets');
define('DATEKIT_ICON', DATEKIT_URL . 'assets/images/icon.png');
define('DATEKIT_PBASE', plugin_basename(__FILE__));

class Datekit
{
    const MINIMUM_PHP_VERSION = '7.4';
    private const VERSION = '1.3.2';

    public function __construct()
    {
        $GLOBALS['version'] = self::VERSION;

        // Check PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_php_version'));
            return;
        }

        // Autoload dependencies
        require_once('vendor/autoload.php');

        // Action links
        add_filter('plugin_action_links_'.DATEKIT_PBASE, array($this, 'jalali_cal_plugin_action_links_callback'));

        // Load functions
        if (!class_exists('Datekit_Function')) {
            require_once DATEKIT_FUNC . '/functions.php';
        }

        // Load plugin text domain
        add_action("plugins_loaded", array($this, 'jalali_cal_action_plugin_loaded'));

        
        // Register uninstall hook
        register_uninstall_hook(__FILE__, array($this, 'jalali_cal_remove_options'));
    }

    public function jalali_cal_action_plugin_loaded(): void {
        load_plugin_textdomain("mo-jalali-calendar", false, dirname(plugin_basename(__FILE__)).'/languages');
    }

    public function jalali_cal_plugin_action_links_callback($links) {
        $settings_link = sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url(admin_url('admin.php?page=jalali_cal')),
            esc_html__('Settings', 'mo-jalali-calendar') 
        );

        array_unshift($links, $settings_link);
        return $links;
    }

    public function admin_notice_minimum_php_version() {
        $message = sprintf(
            esc_html__('requires "%1$s" version %2$s or greater.', 'mo-jalali-calendar'), 
            '<strong>' . esc_html__('PHP', 'mo-jalali-calendar') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        $html_message = sprintf(
            '<div class="notice notice-warning is-dismissible jalali-cal-notice"><p>%1$s</p></div>',
            $message
        );
        echo wp_kses_post($html_message);
    }

    public function jalali_cal_remove_options() {
        delete_option('jalali_cal_wc_datepick');
        delete_option('jalali_cal_wp_presiandate');
        delete_option('jalali_cal_persian');
    }

}
new Datekit();
