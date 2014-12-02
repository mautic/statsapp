<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

$view->extend('StatsAppCoreBundle:Base:index.html.php') ?>

<h1>Application Statistics for <?php echo $application; ?></h1>

<div class="row">
    <div id="appVersions" class="col-md-6" style="min-height:400px"></div>
    <div id="phpVersions" class="col-md-6" style="min-height:400px"></div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($){
        var appChart = new CanvasJS.Chart("appVersions", {
            title: {
                text: "Installed Versions"
            },
            data: [{
                type: "doughnut",
                dataPoints: [
                    <?php foreach ($data['version'] as $point) : ?>
                    {
                        y: <?php echo $point['count']; ?>,
                        indexLabel: '<?php echo $point['name']; ?>'
                    },
                    <?php endforeach; ?>
                ]
            }]
        });
        appChart.render();

        var phpChart = new CanvasJS.Chart("phpVersions", {
            title: {
                text: "PHP Versions"
            },
            data: [{
                type: "doughnut",
                dataPoints: [
	                <?php foreach ($data['phpVersion'] as $point) : ?>
                    {
	                    y: <?php echo $point['count']; ?>,
                        indexLabel: '<?php echo $point['name']; ?>'
                    },
                    <?php endforeach; ?>
                ]
            }]
        });
        phpChart.render();
    });
</script>
