<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title><?php $view['slots']->output('title', 'Welcome!') ?></title>
        <link rel="icon" type="image/x-icon" href="<?php echo $view['assets']->getUrl('favicon.ico') ?>" />
        <link rel="apple-touch-icon" href="<?php echo $view['assets']->getUrl('apple-touch-icon.png') ?>" />
    </head>
    <body>
        <?php $view['slots']->output('_content') ?>
    </body>
</html>

