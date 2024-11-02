<?php

use Liteshamsi\admin\Molsc_Datekit_InParsian;
if (!defined('ABSPATH')) {
    exit;
}

class Molsc_Datekit_Function
{
    public function __construct()
    {
        $this->init_hooks();
    }

    private function init_hooks()
    {
        $molsc_wp_persian_ck = !empty(get_option('molsc_wp_presiandate'))
            ? get_option('molsc_wp_presiandate') == 1
            : 0;

        if (!class_exists('Molsc_Datekit_InParsian')) {
            require_once MOLSC_DATEKIT_FUNC . '/persian-date.php';
        }

        if ($molsc_wp_persian_ck) {
            Molsc_Datekit_InParsian::instance();
        }

        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_menu', array($this, 'admin_menu_callback'));
    }

    public function enqueue_admin_scripts()
    {
        $molsc_wc_datepicker = get_option('molsc_wc_datepicker', 0);

        if ($molsc_wc_datepicker) {
            $this->enqueue_datepicker_assets();
        }

        wp_enqueue_style('molsc-admin-style', MOLSC_DATEKIT_ASSETS . '/css/admin-style.css');
        wp_enqueue_script('molsc-admin-script', MOLSC_DATEKIT_ASSETS . '/js/admin-script.js', ['jquery'], '', true);

        wp_register_style('molsc_inline_admin_styles', false);
        wp_enqueue_style('molsc_inline_admin_styles');


        if (get_option('molsc_persian', 0) == 1) {
            $custom_css = "
                body,body * {font-family: 'vazir';}
                .rtl h1, .rtl h2, .rtl h3, .rtl h4, .rtl h5, .rtl h6 {
                    font-family: 'vazir',sans-serif;
                    font-weight: 600;
                }
            ";
            wp_add_inline_style('molsc_inline_admin_styles', $custom_css);
        }
        
        if (get_option('molsc_wp_presiandate', 0) == 1) {
            $datepicker_css = "
                #ui-datepicker-div { display: none !important; }
            ";
            wp_add_inline_style('molsc_inline_admin_styles', $datepicker_css);
        }
    }

    private function enqueue_datepicker_assets()
    {
        wp_enqueue_style('molsc-datepicker-css', MOLSC_DATEKIT_ASSETS . '/js/datepicker/jalalidatepicker.min.css');
        wp_enqueue_script('molsc-datepicker-js', MOLSC_DATEKIT_ASSETS . '/js/datepicker/jalalidatepicker.min.js', ['jquery'], '', true);
        wp_enqueue_script('molsc-datepicker-classic', MOLSC_DATEKIT_ASSETS . '/js/datepicker/jalali-datepicker.js', ['jquery'], null, true);
        wp_enqueue_script('molsc-admin-date', MOLSC_DATEKIT_ASSETS . '/js/admin-date.js', ['jquery'], '', true);
        wp_enqueue_script('molsc-admin-product', MOLSC_DATEKIT_ASSETS . '/js/admin-shamsi.js', ['molsc-admin-date'], '', true);
    }

    public function admin_menu_callback()
    {
        $menu_suffix = add_menu_page(
            esc_html__('LiteShamsi', 'mo-jalali-calendar'),
            esc_html__('LiteShamsi', 'mo-jalali-calendar'),
            'manage_options',
            'molsc',
            array($this, 'molsc_admin_page_html'),
            MOLSC_DATEKIT_ICON
        );

        add_action('load-' . $menu_suffix, array($this, 'molsc_settings'));
        add_action('admin_notices', array($this, 'admin_notice_success_save'));
    }

    public function admin_notice_success_save()
    {
        if (isset($_POST['lite_shamsi_submit']) && check_admin_referer('_wpnonce')) {
            echo wp_kses_post('<div class="notice notice-success is-dismissible ' . (strpos(get_locale(), 'fa') === 0 ? 'jalali-cal-notice-rtl' : 'jalali-cal-notice-ltr') . '">
            <p>' . esc_html__('Settings have been saved', 'mo-jalali-calendar') . '</p>
            </div>');
        }
    }

    public function molsc_settings()
    {
        if (isset($_POST['lite_shamsi_submit']) && check_admin_referer('_wpnonce')) {
            $molsc_wp_presiandate = isset($_POST['molsc_wp_presiandate']) ? sanitize_text_field($_POST['molsc_wp_presiandate']) : 0;
            $molsc_wc_datepicker = isset($_POST['molsc_wc_datepicker']) ? sanitize_text_field($_POST['molsc_wc_datepicker']) : 0;
            $molsc_persian = isset($_POST['molsc_persian']) ? sanitize_text_field($_POST['molsc_persian']) : 0;
        
            update_option('molsc_wp_presiandate', $molsc_wp_presiandate);
            update_option('molsc_wc_datepicker', $molsc_wc_datepicker);
            update_option('molsc_persian', $molsc_persian);
        }
    }

    public function molsc_admin_page_html()
    {
        require_once MOLSC_DATEKIT_FUNC . '/template/admin-panel.php';
    }
}

new Molsc_Datekit_Function();
