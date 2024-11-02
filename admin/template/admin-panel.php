<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap <?php strpos(get_locale(), 'fa') === 0 ? 'mo-jalali-calendar-rtl' : 'mo-jalali-calendar-ltr' ?> ">
    <div class="flex-container">
        <h2><?php global $title;
            echo esc_html($title);
            ?></h2>
           <h2><?php echo esc_html__('Version : ', 'mo-jalali-calendar') . esc_html($GLOBALS['molscversion']); ?></h2>
    </div>
    <form method="post" class="block-container">
        <table class="form-table">
            <tbody>
                <?php
                function molsc_render_checkbox_field($option_name, $label, $title)
                {
                    $checked = get_option($option_name, 0) == 1 ? 'checked' : '';
                    echo
                    "<tr>
                        <td class='td-full'>
                            <h2 class='title'>".esc_html($title)."</h2>
                            <label class='switch' for='".esc_attr($option_name)."'>
                                <input name='".esc_attr($option_name)."' id='".esc_attr($option_name)."' type='checkbox' value='1' " . esc_attr($checked) . ">
                                <span class='slider'></span>
                            </label>
                            <span class='switch-label'>".esc_html($label)."</span><br />
                        </td>
                    </tr>";
                }
                molsc_render_checkbox_field(
                    'molsc_wp_presiandate',
                    esc_html__('Convert WordPress core date from Gregorian to Jalali', 'mo-jalali-calendar'),
                    esc_html__('Convert WordPress core date to Jalali', 'mo-jalali-calendar')
                );
                if (in_array('woocommerce/woocommerce.php', get_option('active_plugins'))) {
                    molsc_render_checkbox_field(
                        'molsc_wc_datepicker',
                        esc_html__('Convert WordPress date pickers from Gregorian to Jalali', 'mo-jalali-calendar'),
                        esc_html__('Convert Gregorian date pickers to Jalali', 'mo-jalali-calendar')
                    );
                }
                molsc_render_checkbox_field(
                    'molsc_persian',
                    esc_html__('Make the panel font and typography Persian', 'mo-jalali-calendar'),
                    esc_html__('Convert Latin numbers in the panel to Persian', 'mo-jalali-calendar')
                );

                ?>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="lite_shamsi_submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Save', 'mo-jalali-calendar') ?>">
            <?php wp_nonce_field('_wpnonce', '_wpnonce'); ?>
        </p>
    </form>
</div>