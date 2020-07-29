<?php
/**
 * Mortgage widget template.
 *
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $property;

$currency = opalestate_currency_symbol();

wp_enqueue_script( 'opalestate-mortgage-calculator', OPALESTATE_PLUGIN_URL . 'assets/js/mortgage.js', [ 'jquery' ], OPALESTATE_VERSION, true );
wp_enqueue_style( 'opalestate-mortgage-calculator', OPALESTATE_PLUGIN_URL . 'assets/mortgage.css', [], OPALESTATE_VERSION );

$deposit_color = apply_filters( 'opalestate_deposit_color', '#2f73e9' );

wp_localize_script( 'opalestate-scripts', 'opalestate_mortgage',
	[
		'ajax_url'      => admin_url( 'admin-ajax.php' ),
		'currency'      => esc_attr( $currency ),
		'deposit_color' => $deposit_color,
	]
);

$max_price = (int) ( $property && $property->get_price() ) ? $property->get_price() : opalestate_options( 'search_max_price', 10000000 );
$max_price = str_replace( [ ",", "." ], "", $max_price );

$start_price = apply_filters( 'opalestate_mortgage_start_price', $max_price );

$max_price = apply_filters( 'opalestate_mortgage_max_price', $max_price + ( $max_price * 20 / 100 ) );

$rate_start               = apply_filters( 'opalestate_mortgage_rate_start', 10 );
$interest_rate_start      = $rate_start / 100;
$years_start              = apply_filters( 'opalestate_mortgage_years_start', 2 );
$deposit_start            = apply_filters( 'opalestate_mortgage_deposit_start', $max_price / 2 );
$loan_amount              = $start_price - $deposit_start;
$interest_rate_month      = $interest_rate_start / 12;
$number_of_payments_month = $years_start * 12;
$monthly                  = round( ( $loan_amount * $interest_rate_month ) / ( 1 - pow( 1 + $interest_rate_month, -$number_of_payments_month ) ), 2 );

$total           = $deposit_start + ( $monthly * $number_of_payments_month );
$price_percent   = $loan_amount / $total * 100;
$deposit_percent = $deposit_start / $total * 100;

$data_sale_price = [
	'id'         => 'sale_price',
	'decimals'   => opalestate_get_price_decimals(),
	'unit'       => $currency,
	'ranger_min' => 0,
	'ranger_max' => $max_price,
	'input_min'  => 0,
	'input_max'  => $max_price,
	'mode'       => 1,
	'start'      => $start_price,
];

$data_deposit = [
	'id'         => 'deposit',
	'decimals'   => opalestate_get_price_decimals(),
	'unit'       => $currency,
	'ranger_min' => 0,
	'ranger_max' => $max_price,
	'input_min'  => 0,
	'input_max'  => $max_price,
	'mode'       => 1,
	'start'      => $deposit_start,
];

$data_interest_rate = [
	'id'         => 'interest_rate',
	'decimals'   => 2,
	'unit'       => '%',
	'ranger_min' => 0,
	'ranger_max' => 100,
	'input_min'  => 0,
	'input_max'  => 100,
	'mode'       => 1,
	'start'      => $rate_start,
	'step'       => 0.05,
];

$data_years = [
	'id'         => 'years',
	'decimals'   => 1,
	'ranger_min' => 0,
	'ranger_max' => 30,
	'input_min'  => 0,
	'input_max'  => 30,
	'mode'       => 1,
	'start'      => $years_start,
	'step'       => 0.5,
];

if ( opalestate_options( 'currency_position', 'before' ) === 'before' ) {
	$data_sale_price['unit_position'] = 'prefix';
	$data_deposit['unit_position']    = 'prefix';
}

?>
<div class="opalestate-box-content box-mortgage">
    <h4 class="outbox-title"><?php esc_html_e( 'Mortgage Payment Calculator', 'opalestate-pro' ); ?></h4>
    <div class="opalestate-box">
        <div class="opalestate-mortgage-widget-wrap">
            <form class="opalestate-mortgage-form">
                <div class="opalestate-mortgage-chart-container">
                    <div class="opalestate-mortgage-chart">
                        <div class="opalestate-mortgage-chart-svg">
                            <svg viewBox="0 0 64 64" class="pie">
                                <circle r="25%" cx="50%" cy="50%" style="stroke-dasharray: <?php echo esc_attr( $price_percent ); ?> 100">
                                </circle>
                                <circle r="25%" cx="50%" cy="50%"
                                        style="stroke-dasharray: <?php echo esc_attr( $deposit_percent ); ?> 100; stroke: <?php echo esc_attr( $deposit_color ); ?>; stroke-dashoffset:
                                                -<?php echo esc_attr( $price_percent ); ?>; animation-delay: 0.25s">
                                </circle>
                            </svg>
                        </div>
                    </div>
                    <div class="opalestate-mortgage-chart-desc">
                        <div class="opalestate-mortgage-chart-results">
                            <div class="opalestate-mortgage-output">
                                <div class='opalestate-mortgage-output-item opalestate-monthly'>
                                    <label> <?php esc_html_e( 'Your payment', 'opalestate-pro' ); ?></label>
                                    <span class="opalestate-monthly-value">
                                        <?php echo esc_html( $currency ); ?><?php echo esc_html( $monthly ); ?>
                                    </span> /
                                    <small><?php esc_html_e( 'month', 'opalestate-pro' ); ?></small>
                                </div>
                                <div class='opalestate-mortgage-output-item opalestate-loan-amount'>
                                    <label><?php esc_html_e( 'Loan Amount', 'opalestate-pro' ); ?></label>
                                    <span class="opalestate-loan-amount-value">
                                        <?php echo esc_html( $currency ); ?><?php echo esc_html( $loan_amount ); ?>
                                    </span>
                                </div>
                                <div class="opalestate-mortgage-chart-notice">
                                    <ul>
                                        <li><span style="background-color:#02ce76;"></span><?php esc_html_e( 'Your price', 'opalestate-pro' ); ?></li>
                                        <li><span style="background-color:<?php echo esc_attr( $deposit_color ); ?>;"></span><?php esc_html_e( 'Your deposit', 'opalestate-pro' ); ?></li>
                                        <li><span style="background-color:#f06;"></span><?php esc_html_e( 'Your interest', 'opalestate-pro' ); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="opalestate-mortgage-form-inputs">
                    <div class="form-group opalestate-mortgage-item">
                        <div class="opalestate-mortgage-label">
							<?php opalesate_property_slide_ranger_template( esc_html__( 'Sale Price', 'opalestate-pro' ), $data_sale_price ); ?>
                        </div>
                    </div>
                    <div class="form-group opalestate-mortgage-item">
                        <div class="opalestate-mortgage-label">
							<?php opalesate_property_slide_ranger_template( esc_html__( 'Deposit', 'opalestate-pro' ), $data_deposit ); ?>
                        </div>
                    </div>

                    <div class="form-group opalestate-mortgage-item">
                        <div class="opalestate-mortgage-label">
							<?php opalesate_property_slide_ranger_template( esc_html__( 'Annual Interest', 'opalestate-pro' ), $data_interest_rate ); ?>
                        </div>
                    </div>

                    <div class="form-group opalestate-mortgage-item">
                        <div class="opalestate-mortgage-label">
							<?php opalesate_property_slide_ranger_template( esc_html__( 'Years', 'opalestate-pro' ), $data_years ); ?>
                        </div>
                    </div>

                    <div class="mortgage-notes">
                        <span><?php esc_html_e( 'All calculation are based on tentative and estimated figure and shall not replace any financial advice', 'opalestate-pro' ); ?></span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
