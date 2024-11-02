<?php
namespace Liteshamsi\admin;
use Morilog\Jalali\Jalalian;


if (!defined('ABSPATH')) {
    exit;
}

class Molsc_Datekit_InParsian
{
    private static $instance = null;
    public function __construct()
    {
        global $wp_version;

        add_action('woocommerce_process_shop_order_meta', array($this, 'molsc_process_shop_order_meta_callback'), 100, 1);
        add_action('woocommerce_process_product_meta', array($this, 'molsc_process_product_meta_callback'), 100, 1);
        add_action('woocommerce_process_shop_coupon_meta', array($this, 'molsc_process_shop_coupon_meta_callback'), 100, 1);

        add_filter('wp_date', array($this, 'molsc_wp_date_callback'), 100, 4);

        if (version_compare($wp_version, '5.3', '<')) {
            add_filter('date_i18n', array($this, 'molsc_date_i18n_callback'), 100, 4);
        }

    }
    /* 
    This method examines and processes the date of the
    orders and converts the dates to the Jalali format.
    */
    public function molsc_process_shop_order_meta_callback($order_id)
    {
        if (!isset($_POST['order_date'])) return false;

        $order = wc_get_order($order_id);

        $hour   = str_pad(intval($_POST['order_date_hour']), 2, 0, STR_PAD_LEFT);
        $minute = str_pad(intval($_POST['order_date_minute']), 2, 0, STR_PAD_LEFT);
        $second = str_pad(intval($_POST['order_date_second']), 2, 0, STR_PAD_LEFT);

        $timestamp = wc_clean($_POST['order_date']) . " {$hour}:{$minute}:{$second}";

        $jalaliDate = Jalalian::fromFormat('Y-m-d H:i:s', self::english($timestamp));

        if (empty($_POST['order_date'])) {
            $date = time();
        } else {
            $date = gmdate('Y-m-d H:i:s', $jalaliDate->toCarbon()->timestamp);
        }

        $props['date_created'] = $date;


        $order->set_props($props);
        $order->save();
    }

    /* 
    This method processes the product discount dates and converts them
    into Jalali format.
    */
    public function molsc_process_product_meta_callback($product_id)
    {
        $props = [];

        if (isset($_POST['_sale_price_dates_from'])) {
            $date_on_sale_from = wc_clean(wp_unslash($_POST['_sale_price_dates_from']));

            if (!empty($date_on_sale_from)) {
                $jalaliDate = Jalalian::fromFormat('Y-m-d', self::english($date_on_sale_from));
                $props['date_on_sale_from'] = date('Y-m-d 00:00:00', $jalaliDate->toCarbon()->timestamp);
            }
        }
        if (isset($_POST['_sale_price_dates_to'])) {
            $date_on_sale_to = wc_clean(wp_unslash($_POST['_sale_price_dates_to']));

            if (!empty($date_on_sale_to)) {
                $jalaliDate = Jalalian::fromFormat('Y-m-d',self::english($date_on_sale_to));
                $props['date_on_sale_to'] = date('Y-m-d 23:59:59', $jalaliDate->toCarbon()->timestamp);
            }
        }

        if (!count($props)) {
            return false;
        }

        $product = wc_get_product($product_id);
        $product->set_props($props);
        $product->save();
    }

    /*
    Convert WordPress core dates to Jalali with full month name display
    */
    public function molsc_wp_date_callback($date, $format, $timestamp, $timezone)
    {
        $format = str_replace('M', 'F', $format);

        try {
            return Jalalian::fromDateTime($timestamp, $timezone)->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }

    /* 
    Setting the time zone of Iran
    */
    public function molsc_date_i18n_callback($date, $format, $timestamp, $gmt)
    {
        $timezone = get_option('timezone_string', 'Asia/Tehran');

        if (empty($timezone)) {
            $timezone = 'Asia/Tehran';
        }

        $timezone = new \DateTimeZone($timezone);

        return $this->molsc_wp_date_callback($date, $format, $timestamp, $timezone);
    }

    /* 
    This method checks the expiration date of the coupons and converts
    them into Jalali format.
    */
    public function molsc_process_shop_coupon_meta_callback($coupon_id)
    {
        if (!isset($_POST['expiry_date'])) {
            return false;
        }

        $coupon = new \WC_Coupon($coupon_id);
        $expiry_date = wc_clean($_POST['expiry_date']);

        if (!empty($expiry_date)) {
            $jalaliDate = Jalalian::fromFormat('Y-m-d', self::english($expiry_date));
            $expiry_date = $jalaliDate->toCarbon()->format('Y-m-d');
        }

        $coupon->set_props([
            'date_expires' => $expiry_date,
        ]);

        $coupon->save();
    }
    
    /*  
    Convert number(user input) to Latin to perform mathematical calculations
    */
    private static function english($number)
    {
        return str_replace(['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'], range(0, 9), $number);
    }

    /* Use Singltone */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

