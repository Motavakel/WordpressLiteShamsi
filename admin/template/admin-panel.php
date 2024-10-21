<div class="wrap <?php strpos(get_locale(), 'fa') === 0 ? 'lite-shamsi-rtl' : 'lite-shamsi-ltr' ?> ">
    <div class="flex-container">
        <h2><?php global $title;
            echo esc_html($title);
            ?></h2>
            <h2><?php echo esc_html__('Version : ', 'lite-shamsi') . $GLOBALS['version']; ?></h2>
    </div>
    <form method="post" class="block-container">
        <table class="form-table">
            <tbody>
                <?php
                function render_checkbox_field($option_name, $label, $title)
                {
                    $checked = get_option($option_name, 0) == 1 ? 'checked' : '';
                    echo
                    "<tr>
                        <td class='td-full'>
                            <h2 class='title'>$title</h2>
                            <label class='switch' for='$option_name'>
                                <input name='$option_name' id='$option_name' type='checkbox' value='1' " . esc_attr($checked) . ">
                                <span class='slider'></span>
                            </label>
                            <span class='switch-label'>$label</span><br />
                        </td>
                    </tr>";
                }
                render_checkbox_field(
                    'jalali_cal_wp_presiandate',
                    esc_html__('Convert WordPress core date from Gregorian to Jalali', 'lite-shamsi'),
                    esc_html__('Convert WordPress core date to Jalali', 'lite-shamsi')
                );
                if (in_array('woocommerce/woocommerce.php', get_option('active_plugins'))) {
                    render_checkbox_field(
                        'jalali_cal_wc_datepick',
                        esc_html__('Convert WordPress date pickers from Gregorian to Jalali', 'lite-shamsi'),
                        esc_html__('Convert Gregorian date pickers to Jalali', 'lite-shamsi')
                    );
                }
                render_checkbox_field(
                    'jalali_cal_persian',
                    esc_html__('Make the panel font and typography Persian', 'lite-shamsi'),
                    esc_html__('Convert Latin numbers in the panel to Persian', 'lite-shamsi')
                );

                ?>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="lite_shamsi_submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Save', 'lite-shamsi') ?>">
            <?php wp_nonce_field('_wpnonce', '_wpnonce'); ?>
        </p>
    </form>
</div>