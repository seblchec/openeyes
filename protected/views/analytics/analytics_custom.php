
<div id="js-hs-chart-analytics-clinical-others" style="display: none;" class="js-hs-chart-analytics-clinical">
    <div id="js-hs-chart-analytics-clinical-others-right" style="display: block"></div>
    <div id="js-hs-chart-analytics-clinical-others-left" style="display: none"></div>
</div>
<script type="text/javascript">
    var custom_layout, custom_data;
    $(document).ready(function () {

        custom_layout = JSON.parse(JSON.stringify(analytics_layout));
        custom_data = <?= CJavaScript::encode($custom_data); ?>;

        window.csv_data_for_report['custom_data'] = custom_data['csv_data'];
        custom_layout['xaxis']['title'] = "Time post procedure (weeks)";
        custom_layout['xaxis']['rangeslider'] = {};
        custom_layout['yaxis']['title'] = getVATitle();

      //Set VA unit tick labels
      var va_mode = $('#js-chart-filter-plot');
      if (va_mode.html().includes('absolute')) {
        custom_layout['yaxis']['tickmode'] = 'array';
        custom_layout['yaxis']['tickvals'] = <?= CJavaScript::encode($va_final_ticks['tick_position']); ?>;
        custom_layout['yaxis']['ticktext'] = <?= CJavaScript::encode($va_final_ticks['tick_labels']); ?>;
      } else {
        custom_layout['yaxis']['tickmode'] = 'auto';
      }

        custom_layout['yaxis2'] = {
            title: '<?=  $specialty=="Glaucoma"?"IOP (mm Hg)":"CRT &mu;m" ?>',
            titlefont: {
                family: 'sans-serif',
                size: 12,
                color: '#fff',
            },
            side: 'right',
            overlaying: 'y',
            linecolor: '#fff',
            tickcolor: '#fff',
            tickfont: {
                color: '#fff',
            },
        };
        plot(true,custom_layout,custom_data[1]);
        plot(false,custom_layout,custom_data[0])
    });
    function plot(right,custom_layout, custom_data){
        var id;
        if (right){
            id = 'js-hs-chart-analytics-clinical-others-right';
            custom_layout['title'] = "Clinical Section (Right Eye)";
        } else {
            id = 'js-hs-chart-analytics-clinical-others-left';
            custom_layout['title'] = "Clinical Section (Left Eye)";
        }

        var custom_plot = document.getElementById(id);
        Plotly.newPlot(
            id, custom_data ,custom_layout, analytics_options
        );


        custom_plot.on('plotly_click', function (data) {
            for (var i = 0; i < data.points.length; i++) {
                $('.analytics-charts').hide();
                $('.analytics-patient-list').show();
                $('.analytics-patient-list-row').hide();
                var patient_show_list = data.points[i].customdata;
                for (var j = 0; j < patient_show_list.length; j++) {
                    $('#' + patient_show_list[j]).show();
                }
            }
        });
    }
</script>
