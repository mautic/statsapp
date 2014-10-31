<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsApp\ChartsBundle\Controller;

use StatsApp\CoreBundle\Controller\BaseController;

/**
 * Class StatsController
 */
class StatsController extends BaseController
{
    public function sendAction()
    {
        $data = array();
        $code = 200;

        // Only authorize POST requests
        if ($this->request->getMethod() != 'POST') {
            $data['message'] = 'Only POST requests are allowed';
            $code            = 500;
        } else {

        }

        return $this->sendJsonResponse($data, $code);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction()
    {
        return $this->render('StatsAppChartsBundle:Stats:index.html.php');
    }
}
