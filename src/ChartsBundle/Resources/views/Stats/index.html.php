<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

$view->extend('StatsAppCoreBundle:Base:index.html.php') ?>

<h1>Application Statistics</h1>
<table>
    <thead>
        <tr>
            <th>Application</th>
            <th># of Installations</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($applications as $application) : ?>
            <tr>
                <td><?php echo $application['name']; ?></td>
                <td><?php echo $application['installs']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
