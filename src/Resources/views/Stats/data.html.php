<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

$view->extend('StatsAppBundle:Base:index.html.php') ?>

<h1>Application Statistics for <?php echo $application; ?></h1>

<?php if (!empty($data)) : ?>
    <?php $class = (isset($data['version']) && isset($data['phpVersion'])) ? 'col-md-6' : 'col-md-12'; ?>
<div class="row">
    <?php if (isset($data['version'])) : ?>
    <div id="appVersions" class="<?php echo $class; ?>" style="min-height:400px"></div>
    <?php endif; ?>
    <?php if (isset($data['phpVersion'])) : ?>
    <div id="phpVersions" class="<?php echo $class; ?>" style="min-height:400px"></div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($){
    <?php if (isset($data['version'])) : ?>
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
    <?php endif; ?>
    <?php if (isset($data['phpVersion'])) : ?>
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
    <?php endif; ?>
    });
</script>
<?php else : ?>
<div class="alert alert-info">No data found for <?php echo $application; ?></div>
<?php endif;
