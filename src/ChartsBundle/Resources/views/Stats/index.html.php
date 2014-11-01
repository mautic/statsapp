<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

$view->extend('StatsAppCoreBundle:Base:index.html.php') ?>

<h1>Hello World!</h1>
<?php if (isset($application)) : ?>
<h2>Stats for <?php echo ucfirst($application); ?></h2>
<?php endif; ?>
