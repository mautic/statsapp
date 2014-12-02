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

    /**
     * Displays the stats data for a specific application
     *
     * @param string $app
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function appAction($app)
    {
        // Check if the application is supported
        $supported = $this->factory->getParameter('supported_applications');

        if (!in_array($app, $supported)) {
            $this->createNotFoundException(sprintf('The %s application is not supported.', $app));
        }

        /** @var \StatsApp\ChartsBundle\Model\StatsModel $model */
        $model   = $this->factory->getModel('charts.stats');
        $repo    = $model->getRepository();
        $appData = $repo->getAppData($app);

        if (empty($appData)) {
            $this->createNotFoundException(sprintf('No data was found for the %s application.', $app));
        }

        $chartData = [
            'phpVersion' => [],
            'dbDriver' => [],
            'dbVersion' => [],
            'version' => [],
            'serverOs' => []
        ];

        foreach ($appData as $item) {
            foreach ($chartData as $key => $value) {
                if (!isset($chartData[$key][$item[$key]])) {
                    $chartData[$key][$item[$key]] = 0;
                }

                $chartData[$key][$item[$key]]++;
            }
        }

        $data = [];

        foreach ($chartData as $key => $value) {
            foreach ($value as $name => $count) {
                $data[$key][] = [
                    'name'  => $name,
                    'count' => $count
                ];
            }
        }

        $data['total'] = count($appData);

        return $this->render('StatsAppChartsBundle:Stats:data.html.php', [
            'application' => $app,
            'data'        => $data
        ]);
    }

    /**
     * Receives the POSTed data from downstream applications
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function sendAction()
    {
        $data = [];

        // Fetch our data from the POST
        $postData = [
            'application' => $this->request->request->get('application', null),
            'version'     => $this->request->request->get('version', null),
            'phpVersion'  => $this->request->request->get('phpVersion', null),
            'dbDriver'    => $this->request->request->get('dbDriver', null),
            'dbVersion'   => $this->request->request->get('db_version', null),
            'instanceId'  => $this->request->request->get('instanceId', null),
            'serverOs'    => $this->request->request->get('serverOs', null)
        ];

        // Check for null values on the app, version, and instance; everything else we can do without
        if ($postData['application'] === null || $postData['version'] === null || $postData['instanceId'] === null) {
            $data['message'] = 'Missing data from the POST request';

            return $this->sendJsonResponse($data, 500);
        }

        // Check if the application is supported
        $supported = $this->factory->getParameter('supported_applications');

        if (!in_array($postData['application'], $supported)) {
            $data['message'] = sprintf('The %s application is not supported');

            return $this->sendJsonResponse($data, 500);
        }

        /** @var \StatsApp\ChartsBundle\Model\StatsModel $model */
        $model = $this->factory->getModel('charts.stats');

        $entity = $model->getEntity($postData['instanceId'], $postData['application']);

        // Loop over the post data and set it to the entity
        foreach ($postData as $key => $value) {
            $method = 'set' . ucwords($key);
            $entity->$method($value);
        }

        // Save the data
        try {
            $model->saveEntity($entity);

            $data['message'] = 'Data saved successfully';
            $code            = 200;
        } catch (\Exception $exception) {
            $data['message'] = 'An error occurred while saving the data';
            $code            = 500;
        }

        return $this->sendJsonResponse($data, $code);
    }

    /**
     * Displays the stats data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction()
    {
        /** @var \StatsApp\ChartsBundle\Model\StatsModel $model */
        $model = $this->factory->getModel('charts.stats');
        $repo  = $model->getRepository();
        $apps  = $repo->getAppList();

        return $this->render('StatsAppChartsBundle:Stats:index.html.php', [
            'applications' => $apps
        ]);
    }
}
