<?php
global $property;

if ( ! $property->get_block_setting( 'views_statistics' ) ) {
    return;
}

$limit = opalestate_get_option( 'single_views_statistics_limit', 8 );

$stats = new Opalestate_View_Stats( $property->get_id(), $limit );
$array_label  = json_encode( $stats->get_traffic_labels() );
$array_values = json_encode( $stats->get_traffic_data_accordion() );

?>
<div class="opalestate-box-content property-views-statistics-session">
  <h4 class="outbox-title" id="block-statistics"><?php esc_html_e( 'Page Views Statistics', 'opalestate-pro' ); ?></h4>
  <div class="opalestate-box">
      <div class="box-info">
          <canvas id="views-chart"></canvas>
      </div>
  </div>
</div>
<script>
  jQuery(document).ready(function () {
    var ctx = document.getElementById('views-chart').getContext("2d");
    var labels = <?php echo $array_label; ?>;
    var traffic_data = <?php echo $array_values; ?>;
    var label = '<?php esc_html_e( 'Page Views Statistics', 'opalestate-pro' ); ?>';

    var myChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: label,
          borderColor: "#2f73e9",
          pointBorderColor: "#2f73e9",
          pointBackgroundColor: "#2f73e9",
          pointHoverBackgroundColor: "#2f73e9",
          pointHoverBorderColor: "#2f73e9",
          pointBorderWidth: 1,
          pointHoverRadius: 1,
          pointHoverBorderWidth: 1,
          pointRadius: 2,
          fill: false,
          borderWidth: 1,
          data: traffic_data
        }]
      },
      options: {
        legend: {
          position: "none"
        },
        scales: {
          yAxes: [{
            ticks: {
              fontColor: "#7e7e7e",
              fontStyle: "bold",
              beginAtZero: true,
              maxTicksLimit: 5,
              padding: 20
            },
            gridLines: {
              drawTicks: false,
              display: false
            }

          }],
          xAxes: [{
            gridLines: {
              zeroLineColor: "transparent"
            },
            ticks: {
              padding: 20,
              fontColor: "#7e7e7e",
              fontStyle: "bold"
            }
          }]
        }
      }
    });
  });
</script>
