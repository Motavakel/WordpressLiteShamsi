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
        add_action('admin_head', array($this, 'jalali_cal_enqueue_admin_styles'));
    }

    public function enqueue_admin_scripts()
    {
        $jalali_cal_wc_datepicker = get_option('jalali_cal_wc_datepick', 0);

        if ($jalali_cal_wc_datepicker) {
            $this->enqueue_datepicker_assets();
        }

        wp_enqueue_style('jalali_cal-admin-style', DATEKIT_ASSETS . '/css/admin-style.css');
        wp_enqueue_script('jalali_cal-admin-script', DATEKIT_ASSETS . '/js/admin-script.js', ['jquery'], '', true);
    }

    private function enqueue_datepicker_assets()
    {
        wp_enqueue_style('jalali-cal-datepicker', DATEKIT_ASSETS . '/js/datepicker/jalalidatepicker.min.css');
        wp_enqueue_script('jalali-cal-datepicker', DATEKIT_ASSETS . '/js/datepicker/jalalidatepicker.min.js', ['jquery'], '', true);
        wp_enqueue_script('jalali-datepicker', DATEKIT_ASSETS .'/js/datepicker/jalali-datepicker.js', array('jquery'), null, true);
        wp_enqueue_script('lite-shamsi-admin-date', DATEKIT_ASSETS . '/js/admin-date.js', ['jquery'], '', true);
        wp_enqueue_script('lite-shamsi-admin-product', DATEKIT_ASSETS . '/js/admin-shamsi.js', ['lite-shamsi-admin-date'], '', true);
    }

    public function admin_menu_callback()
    {
        $menu_suffix = add_menu_page(
            esc_html__('LiteShamsi', 'lite-shamsi'),
            esc_html__('LiteShamsi', 'lite-shamsi'),
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
            <p>' . esc_html__('Settings have been saved', 'lite-shamsi') . '</p>
            </div>');
        }
    }

    public function jalali_cal_settings()
    {
        if (isset($_POST['lite_shamsi_submit']) && check_admin_referer('_wpnonce')) {
            update_option('jalali_cal_wc_datepick', $_POST['jalali_cal_wc_datepick'] ?? 0);
            update_option('jalali_cal_wp_presiandate', $_POST['jalali_cal_wp_presiandate'] ?? 0);
            update_option('jalali_cal_persian', $_POST['jalali_cal_persian'] ?? 0);
        }
    }

    public function jalali_cal_enqueue_admin_styles()
    {
        if (get_option('jalali_cal_persian', 0) == 1) {
            echo "<style>
                * {font-family: 'vazir';}
            </style>";
        }
        if (get_option('jalali_cal_wp_presiandate', 0) == 1) {
            echo "<style>
                #ui-datepicker-div { display: none !important; }
            </style>";
        }
    }

    public function jalali_cal_admin_page_html()
    {
        require_once DATEKIT_FUNC . '/template/admin-panel.php';
    }
}

new Datekit_Function();
