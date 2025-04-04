<?php
if (!defined('ABSPATH')) {
    exit;
}

class Molsc_Review_Request
{

    public function __construct()
    {
        register_activation_hook(__FILE__, function () {
            if (!get_option('molsc_install_time')) {
                update_option('molsc_install_time', time());
            }
        });

        add_action('admin_notices', [$this, 'molsc_admin_notice']);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_molsc_dismiss_review', [$this, 'dismiss_review']);
    }

    public function dismiss_review()
    {
        if (isset($_POST['molsc_feedback_given']) && $_POST['molsc_feedback_given'] == 'yes') {
            update_option('molsc_gave_feedback', true);
        } else {
            update_option('molsc_install_time', time());
        }
        wp_die();
    }


    public function enqueue_admin_scripts()
    {
        wp_enqueue_script('molsc-admin-review-request', MOLSC_DATEKIT_ASSETS . '/js/review-request.js');
        wp_localize_script('molsc-admin-review-request', 'molsc_ajax', [
            'ajaxurl' => admin_url('admin-ajax.php')
        ]);
    }

    public function molsc_admin_notice()
    {
        $install_time = get_option('molsc_install_time', 0);
        $gave_feedback = get_option('molsc_gave_feedback', false);

        if (!$gave_feedback && (time() - $install_time) >=  DAY_IN_SECONDS * 4 ) {
            echo '<div class="notice notice-info is-dismissible" id="molsc-review-notice">
                <p>از افزونه لایت شمسی لذت می‌برید؟ لطفاً با ثبت نظر در مخزن وردپرس، از ما حمایت کنید!</p>
                <p><a href="https://wordpress.org/plugins/mo-jalali-calendar/#reviews" target="_blank" class="button-primary" id="molsc-give-feedback">ثبت نظر</a></p>
                <p><a href="#" id="molsc-dismiss-review">بعداً یادآوری کن</a></p>
            </div>';
        }
    }
}

new Molsc_Review_Request();