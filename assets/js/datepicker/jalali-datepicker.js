jQuery(function($) {
    let option = {
        separatorChar: "-",
    };

    jalaliDatepicker.startWatch(option);

    // برای صفحه محصول
    $("#_sale_price_dates_from").attr('data-jdp', '');
    $("#_sale_price_dates_to").attr('data-jdp', '');

    $("div.woocommerce_variations").on("click", "a.sale_schedule", function() {
        let el_to = $(this).parent().parent().next().find("input[name*=to]");
        el_to.attr('data-jdp', '');

        let el_from = $(this).parent().parent().next().find("input[name*=from]");
        el_from.attr('data-jdp', '');
    });

    // برای صفحه کوپن
    let expiryDate = $("input[name=expiry_date]").val();
    $("input[name=expiry_date]").attr('data-jdp', '');

    // برای صفحه سفارش
    $("input[name=order_date]").attr('data-jdp', '');

    // برای صفحه گزارش
    $("input[name=start_date]").attr('data-jdp', '');
    $("input[name=end_date]").attr('data-jdp', '');
});
