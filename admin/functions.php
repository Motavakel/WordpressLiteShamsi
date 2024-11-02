<?php
defined('ABSPATH') || exit;

class Datekit_Function
{
    public function __construct()
    {
        $this->init_hooks();
    }

    private function init_hooks()
    {
        $jalali_cal_wp_persian_ck = !empty(get_option('jalali_cal_wp_presiandate'))
            ? get_option('jalali_cal_wp_presiandate') == 1
            : 0;

        if (!class_exists('Datekit_InParsian')) {
            require_once DATEKIT_FUNC . '/persian-date.php';
        }

        if ($jalali_cal_wp_persian_ck) {
            Datekit_InParsian::instance();
        }

        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_menu', array($this, 'admin_menu_callback'));
    }

    public function enqueue_admin_scripts()
    {
        $jalali_cal_wc_datepicker = get_option('jalali_cal_wc_datepick', 0);

        if ($jalali_cal_wc_datepicker) {
            $this->enqueue_datepicker_assets();
        }

        wp_enqueue_style('jalali_cal-admin-style', DATEKIT_ASSETS . '/css/admin-style.css');
        wp_enqueue_script('jalali_cal-admin-script', DATEKIT_ASSETS . '/js/admin-script.js', ['jquery'], '', true);

        wp_register_style('lite_shamsi_inline_admin_styles', false);
        wp_enqueue_style('lite_shamsi_inline_admin_styles');


        if (get_option('jalali_cal_persian', 0) == 1) {
            $custom_css = "
                body,body * {font-family: 'vazir';}
                .rtl h1, .rtl h2, .rtl h3, .rtl h4, .rtl h5, .rtl h6 {
                    font-family: 'vazir',sans-serif;
                    font-weight: 600;
                }
            ";
            wp_add_inline_style('lite_shamsi_inline_admin_styles', $custom_css);
        }
        
        if (get_option('jalali_cal_wp_presiandate', 0) == 1) {
            $datepicker_css = "
                #ui-datepicker-div { display: none !important; }
            ";
            wp_add_inline_style('lite_shamsi_inline_admin_styles', $datepicker_css);
        }
    }

    private function enqueue_datepicker_assets()
    {
        wp_enqueue_style('jalali-cal-datepicker', DATEKIT_ASSETS . '/js/datepicker/jalalidatepicker.min.css');
        wp_enqueue_script('jalali-cal-datepicker', DATEKIT_ASSETS . '/js/datepicker/jalalidatepicker.min.js', ['jquery'], '', true);
        wp_enqueue_script('jalali-datepicker', DATEKIT_ASSETS . '/js/datepicker/jalali-datepicker.js', array('jquery'), null, true);
        wp_enqueue_script('mo-jalali-calendar-admin-date', DATEKIT_ASSETS . '/js/admin-date.js', ['jquery'], '', true);
        wp_enqueue_script('mo-jalali-calendar-admin-product', DATEKIT_ASSETS . '/js/admin-shamsi.js', ['mo-jalali-calendar-admin-date'], '', true);
    }

    public function admin_menu_callback()
    {
        $menu_suffix = add_menu_page(
            esc_html__('LiteShamsi', 'mo-jalali-calendar'),
            esc_html__('LiteShamsi', 'mo-jalali-calendar'),
            'manage_options',
            'jalali_cal',
            array($this, 'jalali_cal_admin_page_html'),
            DATEKIT_ICON
        );

        add_action('load-' . $menu_suffix, array($this, 'jalali_cal_settings'));
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

    public function jalali_cal_settings()
    {
        if (isset($_POST['lite_shamsi_submit']) && check_admin_referer('_wpnonce')) {
            $jalali_cal_wp_presiandate = isset($_POST['jalali_cal_wp_presiandate']) ? sanitize_text_field($_POST['jalali_cal_wp_presiandate']) : 0;
            $jalali_cal_wc_datepick = isset($_POST['jalali_cal_wc_datepick']) ? sanitize_text_field($_POST['jalali_cal_wc_datepick']) : 0;
            $jalali_cal_persian = isset($_POST['jalali_cal_persian']) ? sanitize_text_field($_POST['jalali_cal_persian']) : 0;
        
            update_option('jalali_cal_wp_presiandate', $jalali_cal_wp_presiandate);
            update_option('jalali_cal_wc_datepick', $jalali_cal_wc_datepick);
            update_option('jalali_cal_persian', $jalali_cal_persian);
        }
    }

    public function jalali_cal_admin_page_html()
    {
        require_once DATEKIT_FUNC . '/template/admin-panel.php';
    }
}

new Datekit_Function();
