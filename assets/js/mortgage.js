(function ($) {
    'use strict';
    $(document).ready(function ($) {
        if (typeof opalestate_mortgage !== 'undefined') {
            var currency = opalestate_mortgage.currency;

            $('.opalestate-mortgage-form input').change(function (e) {
                e.preventDefault();
                var $el = $(this),
                    $widget = $el.closest('.opalestate-mortgage-widget-wrap');

                var sale_price = $widget.find('input[name="sale_price"]').val();
                var precent_down = $widget.find('input[name="deposit"]').val();
                var years = parseInt($widget.find('input[name="years"]').val(), 10);
                var interest_rate = parseFloat($widget.find('input[name="interest_rate"]').val(), 10) / 100;

                var interest_rate_month = interest_rate / 12;
                var number_of_payments_month = years * 12;

                var loan_amount = sale_price - precent_down;
                var monthly_payment = parseFloat(
                    (loan_amount * interest_rate_month) /
                    (1 - Math.pow(1 + interest_rate_month, -number_of_payments_month)))
                    .toFixed(2);

                if (monthly_payment === 'NaN') {
                    monthly_payment = 0;
                }

                var total = parseFloat(precent_down) + parseFloat(monthly_payment * number_of_payments_month);
                var price_percent = loan_amount / total * 100;
                var deposit_percent = precent_down / total * 100;

                $widget.find('.opalestate-monthly-value').html(currency + monthly_payment);

                $widget.find('.opalestate-loan-amount-value').html(currency + loan_amount);

                $widget.find('.opalestate-mortgage-chart-svg').html(
                    '<svg viewBox=\'0 0 64 64\' class=\'pie\'>' +
                    '<circle r=\'25%\' cx=\'50%\' cy=\'50%\' style=\'stroke-dasharray: ' + price_percent + ' 100\'>' +
                    '</circle>' +
                    '<circle r=\'25%\' cx=\'50%\' cy=\'50%\' style=\'stroke-dasharray: ' + deposit_percent + ' 100;' +
                    ' stroke:' + opalestate_mortgage.deposit_color + '; stroke-dashoffset: -' + price_percent + ';animation-delay: 0.25s\'>' +
                    '</circle>' +
                    '</svg>'
                );
            });
        }
    });
})(jQuery);
