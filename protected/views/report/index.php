<div class="container">
    <?php
    $this->widget('TitleBreadcrumb', [
        'pageTitle' => 'Report',
        'breadcrumbItems' => [
            ['label' => 'Admin', 'href' => '/site/admin'],
            ['isActive' => true, 'label' => 'Report'],
        ]
    ]);
    ?>

    <div class="section form row">

        <!-- NOTE leaving following code as comment as it seems related to missing scripts -->
        <!-- <div class="chart" id="chart_div" style="width: 600px; height: 400px">
        </div>
        <div class="setting"></div> -->

        <div class="col-md-offset-3 col-md-6">
            <form class="form" method="POST">
                <div class="form-group">
                    <label class="control-label" for="Report_start_date">Start Date</label>
                    <?= CHtml::dateField('Report[start_date]', isset($args['start_date']) ? $args['start_date'] : '', array('class' => 'form-control')) ?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="Report_end_date">End Date</label>
                    <?= CHtml::dateField('Report[end_date]', isset($args['end_date']) ? $args['end_date'] : '', array('class' => 'form-control')) ?>
                </div>
                <div class="form-group">
                    <label class="control-label" for="Report_ids">Display for DOI</label>
                    <?php echo CHtml::dropDownList(
                        'Report[ids][]',
                        empty($selectDois) ? 'all' : $selectDois,
                        $dois,
                        array('class' => 'js-multi form-control', 'multiple' => 'multiple', 'data-placeholder' => Yii::t('app', 'Select DOIs'))
                    ); ?>
                </div>
                <div class="pull-right">
                    <input type="submit" class="btn background-btn" name="report" value="View" />
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// NOTE there are a number of missing scripts here
$clientScript = Yii::app()->clientScript;
$clientScript->registerCssFile('/css/chosen.min.css');
// NOTE /js/chosen.jquery.min.js not found
$clientScript->registerScriptFile('/js/chosen.jquery.min.js', CClientScript::POS_END);


$register_script = <<<EO_SCRIPT
// NOTE chosen is not a function
$(".js-multi").chosen({
	max_selected_options: 10,
	no_results_text: "DOIs not found",
}).change( function(){
 var dois = $(this).val();
 if($.inArray('all',dois) !== -1) {
 	$(this).val('all').trigger("chosen:updated");
 }
});
$(".js-multi").bind("chosen:maxselected", function() {
	alert("You may only select at most 10 DOIs at once");
});
EO_SCRIPT;
$clientScript->registerScript('register_script', $register_script, CClientScript::POS_READY);
?>
<!-- NOTE /js/jsapi.js not found -->
<script type="text/javascript" src="/js/jsapi.js"></script>
<script type="text/javascript">
    // Load the Visualization API and the piechart package.
    // NOTE Google is not defined
    google.load('visualization', '1.0', {
        'packages': ['corechart']
    });

    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart);

    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    function drawChart() {
        // Create the data table.
        var data = new google.visualization.DataTable();

        data.addColumn('string', 'months');
        data.addColumn('number', 'page views');
        data.addColumn('number', 'total visits');
        data.addColumn('number', 'number of unique visitors');

        data.addRows(<?= $linedata ?>);

        // Set chart options
        var options = {
            'title': 'Summary of DOIs',
            'width': 600,
            'height': 400,
            'hAxis': {
                'title': 'Date',
                'logScale': true
            },
            'vAxis': {
                'title': 'View',
                'logScale': false
            }
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);

        // Add Listener to hide columns
        var columns = [];
        var series = {};

        // save columns value
        for (var i = 0; i < data.getNumberOfColumns(); i++) {
            columns.push(i);
            if (i > 0) {
                series[i - 1] = {};
            }
        }

        options['series'] = series;

        google.visualization.events.addListener(chart, 'select', function() {
            var sel = chart.getSelection();
            // if selection length is 0, we deselected an element
            if (sel.length > 0) {
                // if select row is not null, then we hide/show the data series
                if (sel[0].row === null) {
                    var col = sel[0].column;
                    if (columns[col] == col) {
                        // hide the data series
                        columns[col] = {
                            label: data.getColumnLabel(col),
                            type: data.getColumnType(col),
                            calc: function() {
                                return null;
                            }
                        };
                        // grey out the legend entry
                        series[col - 1].color = '#CCCCCC';
                    } else {
                        // show the data series
                        columns[col] = col;
                        series[col - 1].color = null;
                    }
                    var view = new google.visualization.DataView(data);
                    // set the new columns
                    view.setColumns(columns);
                    // redraw the view
                    chart.draw(view, options);
                }
            }
        });
    }
</script>