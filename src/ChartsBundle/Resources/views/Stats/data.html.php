<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

$view->extend('StatsAppCoreBundle:Base:index.html.php') ?>

<h1>Application Statistics for <?php echo $application; ?></h1>
<pre><?php print_r($data); ?></pre>
