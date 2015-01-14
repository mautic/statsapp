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
        <title><?php $view['slots']->output('title', 'Mautic Application Statistics') ?></title>
        <link rel="icon" type="image/x-icon" href="<?php echo $view['assets']->getUrl('favicon.ico') ?>" />
        <link rel="apple-touch-icon" href="<?php echo $view['assets']->getUrl('apple-touch-icon.png') ?>" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" />
	    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/canvasjs/1.4.1/canvas.min.js"></script>
    </head>
    <body>
        <?php $view['slots']->output('_content') ?>
    </body>
</html>

