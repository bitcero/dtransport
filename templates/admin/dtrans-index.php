<h1 class="cu-section-title"><?php _e('Dashboard','dtransport'); ?></h1>

<div class="row dtrans-in-numbers">
    <div class="col-xs-6 col-sm-3">
        <?php
        $counter = $common->widgets()->load('rmcommon', 'TileBox');
        $counter->setup([
            'type' => 'counter',
            'style' => 'icon-right',
            'caption' => __('Total Items', 'dtransport'),
            'icon' => 'svg-rmcommon-squares',
            'counter' => $totalItems,
            'color' => 'cyan',
            'footer' => __('Total of registered items', 'dtransport')
        ]);
        echo $counter->getHtml();
        ?>
    </div>

    <div class="col-xs-6 col-sm-3">
        <?php
        $counter = $common->widgets()->load('rmcommon', 'TileBox');
        $counter->setup([
            'type' => 'counter',
            'style' => 'icon-right',
            'caption' => __('Total Downloads', 'dtransport'),
            'icon' => 'svg-rmcommon-cloud-download',
            'counter' => $totalDowns,
            'color' => 'orange',
            'footer' => sprintf(__('Last 30 days downloads: %s','dtransport'), '<strong>'.$total30.'</strong>')
        ]);
        echo $counter->getHtml();
        ?>
    </div>

    <div class="clearfix visible-xs"></div>

    <div class="col-xs-6 col-sm-3">
        <?php
        $counter = $common->widgets()->load('rmcommon', 'TileBox');
        $counter->setup([
            'type' => 'counter',
            'style' => 'icon-right',
            'caption' => __('Items Waiting', 'dtransport'),
            'icon' => 'svg-rmcommon-sand-clock',
            'counter' => $itemsWaiting,
            'color' => 'purple',
            'footer' => __('Items waiting for approval', 'dtransport')
        ]);
        echo $counter->getHtml();
        ?>
    </div>

    <div class="col-xs-6 col-sm-3">
        <?php
        $counter = $common->widgets()->load('rmcommon', 'TileBox');
        $counter->setup([
            'type' => 'counter',
            'style' => 'icon-right',
            'caption' => __('Total Categories', 'dtransport'),
            'icon' => 'svg-rmcommon-folder',
            'counter' => $totalCats,
            'color' => 'light-green',
            'footer' => sprintf(__('Inactive categories: %s','dtransport'), '<strong>'.$catsInactive.'</strong>')
        ]);
        echo $counter->getHtml();
        ?>
    </div>
</div>

<div class="row" data-news="load" data-boxes="load" data-module="dtransport" data-target="#dtrans-news" data-container="dashboard" data-box="dtransport-dashboard">

    <!-- Last 30 days activity -->
    <div class="size-2" data-dashboard="item">

        <div class="cu-box">
            <div class="box-header">
                <h3 class="box-title">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-bars-chart'); ?>
                    <?php _e('Last 30 days activity','dtransport'); ?>
                </h3>
            </div>
            <div class="box-content" id="last-30">
                <div id="usage-data"></div>
            </div>
            <div class="box-footer">
                <?php _e('This graph shows the total download activity in D-Transport in last 30 days.','dtransport'); ?>
            </div>
        </div>

    </div>

    <!-- Top Downloads -->
    <div class="size-2" data-dashboard="item">
        <div class="cu-box">
            <div class="box-header">
                <h3 class="box-title">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-line-chart'); ?>
                    <?php _e('Top Downloads','dtransport'); ?>
                </h3>
            </div>
            <div class="box-content" id="top-downloads">
                <p class="help-block">
                    <?php _e('Next are the 10 most downloaded files in D-Transport','dtransport'); ?>
                </p>
                <div id="top-graph"></div>
            </div>
        </div>
    </div>

    <?php if($dtSettings->branding): ?>
    <div class="size-1" data-dashboard="item">

        <?php
        $user = $common->widgets()->load('rmcommon', 'UserCard');

        $text = __('Hello %s! I\'m the developer of %s and other cool modules for XOOPS. If you wish to support my work I invite you to purchase a subscription for %s related services.');

        $user->setup([
            'type' => 'large',
            'image' => 'https://www.gravatar.com/avatar/a888698732624c0a1d4da48f1e5c6bb4?s=200',
            'name' => 'Eduardo Cortes',
            'link' => 'https://www.eduardocortes.mx',
            'charge' => 'Web Developer (Freelance)',
            'mainButton' => [
                'caption' => __('Website', 'mywords'),
                'link' => 'https://www.eduardocortes.mx/d-transport/',
                'icon' => 'svg-rmcommon-user'
            ],
            'color' => 'green',
            'highlight' => 'bottom',
            'info' => sprintf($text, '<strong>' . $xoopsUser->getVar('uname') . '</strong>','<strong>D-Transport</strong>', '<strong>D-Transport</strong>'),
            'social' => [
                [
                    'icon' => 'svg-rmcommon-world',
                    'link' => 'https://www.eduardocortes.mx/blog/'
                ],
                [
                    'icon' => 'svg-rmcommon-twitter',
                    'link' => 'https://www.twitter.com/bitcero'
                ],
                [
                    'icon' => 'svg-rmcommon-facebook',
                    'link' => 'https://www.facebook.com/bitcero'
                ],
                [
                    'icon' => 'svg-rmcommon-instagram',
                    'link' => 'https://www.instagram.com/bitcero'
                ],
                [
                    'icon' => 'svg-rmcommon-github',
                    'link' => 'https://www.github.com/bitcero'
                ]
            ],
        ]);
        $user->display();
        ?>

    </div>
    <?php endif; ?>

    <!-- Best rated -->
    <div class="size-1" data-dashboard="item">
        <div class="cu-box">
            <div class="box-header">
                <h3 class="box-title">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-star'); ?>
                    <?php _e('Best Rated','dtransport'); ?>
                </h3>
            </div>
            <div class="box-content" id="top-downloads">
                <table class="table condensed">
                    <thead>
                    <tr>
                        <th ><?php _e('Download Item','dtransport'); ?></th>
                        <th class="text-center"><?php _e('Rating','dtransport'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($bestRated as $item): ?>
                        <tr>
                            <td>
                                <a href="<?php echo $item['link']; ?>"><?php echo $item['name']; ?></a>
                            </td>
                            <td align="center"><?php echo $item['rating'] == 'nan' ? '<em>' . __('Not rated yet', 'dtransport') . '</em>' : $item['rating']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Downloads -->
    <div class="size-1" data-dashboard="item">
        <div class="cu-box">
            <div class="box-header">
                <h3 class="box-title">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-line-chart'); ?>
                    <?php _e('Top Downloads', 'dtransport'); ?>
                </h3>
            </div>
            <div class="box-content">
                <table class="table table-condensed">
                    <thead>
                    <tr>
                        <th><?php _e('Download Item','dtransport'); ?></th>
                        <th align="center"><?php _e('Total Hits','dtransport'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($tops as $top): ?>
                        <tr>
                            <td><a href="<?php echo $top['link']; ?>"><?php echo $top['name']; ?></a></td>
                            <td align="center"><?php echo $top['hits']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="size-1" data-dashboard="item">
        <div class="cu-box">
            <div class="box-header">
                <h3 class="box-title">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-pin'); ?>
                    <?php _e('Latest News','dtransport'); ?>
                </h3>
            </div>
            <div class="box-content">
                <div id="dtrans-news"></div>
            </div>
        </div>
    </div>


</div>

<script type="text/javascript">
    (function($){
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

            var data = [
                <?php foreach($tops as $id => $top): ?>
                {
                    label: "<?php echo $top['name']; ?>",
                    data: [[<?php echo $id; ?>, <?php echo $top['hits']; ?>]]
                },
                <?php endforeach; ?>
            ];

            var plotOptions = {
                series: {
                    bars: {show: true}
                },
                legend: {noColumns: 2},
                yaxis: {min: 0},
                xaxis: {show: false},
                grid: {
                    borderWidth: 1,
                    borderColor: "#D9D9D9",
                    hoverable: true
                },
            };

            var plot1 = $.plot($("#top-graph"), data, plotOptions);
        });
    }(jQuery));
</script>