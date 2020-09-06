<h1 class="cu-section-title"><?php echo sprintf(__('%s Statistics','dtransport'), $item->getVar('name')); ?></h1>

<div class="row">

    <div class="col-md-6">

        <div class="row">

            <div class="col-sm-6">
                <?php
                $counter = $common->widgets()->load('rmcommon', 'TileBox');
                $counter->setup([
                    'type' => 'counter',
                    'style' => 'icon-right',
                    'caption' => __('Total Downloads', 'dtransport'),
                    'icon' => 'svg-rmcommon-cloud-download-o',
                    'counter' => $item->hits,
                    'color' => '',
                    'footer' => sprintf(__('Last 30 days downloads: %s','dtransport'), '<em>'.$total30.'</em>')
                ]);
                echo $counter->getHtml();
                ?>
            </div>

            <div class="col-sm-6">
                <?php
                $counter = $common->widgets()->load('rmcommon', 'TileBox');
                $counter->setup([
                    'type' => 'counter',
                    'style' => 'icon-right',
                    'caption' => __('Files Created','dtransport'),
                    'icon' => 'svg-rmcommon-docs',
                    'counter' => $filesCount,
                    'color' => '',
                    'footer' => sprintf(__('Top file: %s','dtransport'), isset($allFiles[0]) ? '<em>'.$allFiles[0]->getVar('title').'</em>' : '')
                ]);
                echo $counter->getHtml();
                ?>
            </div>

            <div class="col-sm-6">
                <?php

                $rating = number_format($item->getVar('rating') / $item->getVar('votes'), 1);
                $rating = 'nan' == $rating ? '--' : $rating;

                $counter = $common->widgets()->load('rmcommon', 'TileBox');
                $counter->setup([
                    'type' => 'counter',
                    'style' => 'icon-right',
                    'caption' => __('Rating Received','dtransport'),
                    'icon' => 'svg-rmcommon-star',
                    'counter' => $rating,
                    'color' => '',
                    'footer' => sprintf(__('Votes: %s','dtransport'), '<em>'.$item->getVar('votes').'</em>')
                ]);
                unset($rating);
                echo $counter->getHtml();
                ?>
            </div>

            <div class="col-sm-6">
                <?php

                $counter = $common->widgets()->load('rmcommon', 'TileBox');
                $counter->setup([
                    'type' => 'counter',
                    'style' => 'icon-right',
                    'caption' => __('Comments','dtransport'),
                    'icon' => 'svg-rmcommon-comments2',
                    'counter' => $item->comments,
                    'color' => '',
                    'footer' => sprintf(__('Since: %s','dtransport'), '<em>'.$tf->format($item->getVar('created'), '%T% %d%, %Y%').'</em>')
                ]);
                echo $counter->getHtml();
                ?>
            </div>

        </div>

        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $cuIcons->getIcon('svg-rmcommon-cloud-download'); ?> <?php _e('Downloads per File','dtransport'); ?></h3>
            </div>
            <div class="panel-body">
                <p><?php echo sprintf(__('Showing all files from %s. Those files that are not shown have zero downloads.','dtransport'), '<strong>'.$item->getVar('name').'</strong>'); ?></p>
            </div>
            <div class="table-responsive">
                    <table class="table table-bordered" style="width: 100%">
                        <thead>
                        <tr>
                            <th><?php _e('File','dtransport'); ?></th>
                            <th align="center"><?php _e('Hits','dtransport'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($allFiles as $file): ?>
                            <tr>
                                <td><?php echo $file->getVar('title'); ?></td>
                                <td align="center"><?php echo $file->getVar('hits'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
            </div>
        </div>



    </div>

    <div class="col-md-6">

        <!-- Usage -->
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-line-chart'); ?>
                    <?php _e('Last 30 days activity','dtransport'); ?>
                </h3>
            </div>
            <div class="panel-body">
                <div id="last-30" class="graph-content">
                    <p>
                        <?php echo sprintf(__('Next graph shows the total download activity for %s in last 30 days.','dtransport'),
                            '<strong>'.$item->getVar('name').'</strong>'); ?>
                    </p>
                    <hr>
                    <div id="usage-data" class="big-graphs"></div>
                    <p class="help-block">
                        <?php _e('Note that this graph include activity for all files available for this item.','dtransport'); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-line-chart'); ?>
                    <?php _e('Activity per File','dtransport'); ?>
                </h3>
            </div>
            <div class="panel-body">
                <div id="per-file" class="graph-content">
                    <p>
                        <?php echo sprintf(__('Next are the top 5 files from %s.','dtransport'), $item->getVar('name')); ?>
                    </p>
                    <hr>
                    <div id="per-files-graph" class="big-graphs"></div>
                </div>
            </div>
        </div>
    </div>

</div>


<script type="text/javascript">
    $(document).ready(function(){
        var usage = [<?php echo implode(",",$data); ?>];
        var plotOptions = {
            xaxes: [{mode: 'time'}],
            yaxes: [{min: 0}],
            legend: {
                show: false
            },
            series: {
                lines: {
                    show: true,
                    lineWidth: 2,
                    fill: true,
                    fillColor: "rgba(4, 152, 212, 0.4)"
                },
                points: {
                    radius: 3,
                    symbol: "circle",
                    show: true,
                    lineWidth: 2,
                    fill: true,
                    fillColor: "rgba(255,255,255,1)"
                }
            },
            selection: { mode: "x"},
            grid: {
                borderWidth: 1,
                borderColor: "rgba(4, 152, 212, 1)",
                color: "#03739F",
                hoverable: true,
                clickable: true
            },
            xaxis: {
                zoomRange: [1,10]
            },
            yaxis: {
                zoomRange: [1,10]
            },
            zoom: {
                interactive: true
            }
        };
        var plot = $.plot($("#usage-data"), [{
            data: usage,
            label: "<?php _e('Downloads total','dtransport'); ?>",
            color: "rgba(4, 152, 212, 1)",
            hoverable: true
        }], plotOptions);

        var dataFiles = [
            <?php foreach($dataFiles as $id => $data): ?>
            {
                label: "<?php echo $filesObjects[$id]->getVar('title'); ?>",
                data: [<?php echo implode(",", $data); ?>]
            },
            <?php endforeach; ?>
        ];

        var optionsFiles = {
            xaxes: [{mode: 'time'}],
            yaxes: [{min: 0}],
            series: {
                lines: {
                    show: true,
                    lineWidth: 2
                },
                points: {
                    radius: 3,
                    symbol: "circle",
                    show: true,
                    lineWidth: 2
                }
            },
            xaxis: {tickDecimals: 0},
            selection: { mode: "x"},
            grid: {
                borderWidth: 1,
                borderColor: "rgba(4, 152, 212, 1)",
                color: "#03739F",
                hoverable: true,
                clickable: true
            }
        };

        var plot1 = $.plot($("#per-files-graph"), dataFiles, optionsFiles);

        $("#usage-data, #per-files-graph").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));

            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2);

                    showTooltip(item.pageX, item.pageY,
                        "<?php _e('%u Downloads','dtransport'); ?>".replace("%u", y));
                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;
            }

        });

        function showTooltip(x, y, contents) {
            $('<div id="tooltip">' + contents + '</div>').css( {
                position: 'absolute',
                display: 'none',
                top: y + 5,
                left: x + 5,
                'box-shadow': '2px 2px 1px 1px rgba(0,0,0,0.4)',
                padding: '2px 5px',
                'background-color': '#000',
                color: '#FFF',
                'border-radius': '2px',
                opacity: 1
            }).appendTo("body").fadeIn(200);
        }

        $("#per-files-graph").bind("plotselected", function (event, ranges) {


            plot = $.plot("#"+$(this).attr("id"), $(this).attr("id")=='usage-data' ? usage : dataFiles,
                $.extend(true, {}, $(this).attr("id")=='usage-data' ? plotOptions : optionsFiles, {
                    xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
                }));
        });
    });
</script>