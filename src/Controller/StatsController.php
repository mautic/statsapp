<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsAppBundle\Controller;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class StatsController
 */
class StatsController extends BaseController
{
    /**
     * Retrieves the stat data as a JSON string
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dataAction()
    {
        $source = $this->request->query->get('source', null);

        if ($source === 'downloads') {
            return $this->fetchDownloadData();
        }

        $data = $this->fetchData();

        if ($source && isset($data[$source])) {
            $data = $data[$source];
        }

        return $this->sendJsonResponse($data);
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
            'version' => str_replace('-dev', '', $this->request->request->get('version', null)),
            'phpVersion' => $this->request->request->get('phpVersion', null),
            'dbDriver' => $this->request->request->get('dbDriver', null),
            'dbVersion' => $this->request->request->get('dbVersion', null),
            'instanceId' => $this->request->request->get('instanceId', null),
            'serverOs' => $this->request->request->get('serverOs', null)
        ];

        // Check for null values on the app, version, and instance; everything else we can do without
        if ($postData['application'] === null || $postData['version'] === null || $postData['instanceId'] === null) {
            $data['message'] = $this->get('translator')->trans('Missing data from the POST request');

            return $this->sendJsonResponse($data, 500);
        }

        // Check if the application is supported
        $supported = $this->factory->getParameter('supported_applications');

        if (!in_array($postData['application'], $supported)) {
            $data['message'] = $this->get('translator')->trans('The %app% application is not supported', ['%app%' => $postData['application']]);

            return $this->sendJsonResponse($data, 500);
        }

        /** @var \StatsAppBundle\Model\StatsModel $model */
        $model = $this->factory->getModel('stats');

        $entity = $model->getEntity($postData['instanceId'], $postData['application']);

        // Loop over the post data and set it to the entity
        foreach ($postData as $key => $value) {
            $method = 'set' . ucwords($key);
            $entity->$method($value);
        }

        // Save the data
        try {
            $model->saveEntity($entity);

            $data['message'] = $this->get('translator')->trans('Data saved successfully');
            $code = 200;
        } catch (\Exception $exception) {
            $data['message'] = $this->get('translator')->trans('An error occurred while saving the data');
            $code = 500;
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
        try {
            $data = $this->fetchData();
        } catch (NotFoundHttpException $e) {
            $data = [];
        }

        return $this->render('StatsAppBundle:Stats:data.html.php', [
            'application' => 'Mautic',
            'data' => $data
        ]);
    }

    /**
     * Fetches the application data
     *
     * @return array
     *
     * @throws NotFoundHttpException
     */
    private function fetchData()
    {
        /** @var \StatsAppBundle\Model\StatsModel $model */
        $model = $this->factory->getModel('stats');
        $repo = $model->getRepository();
        $appData = $repo->getAppData('Mautic');

        if (empty($appData)) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('No data was found for the %app% application', ['%app%' => $app])
            );
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
                if (!is_null($item[$key])) {
                    if (!isset($chartData[$key][$item[$key]])) {
                        $chartData[$key][$item[$key]] = 0;
                    }

                    $chartData[$key][$item[$key]]++;
                }
            }
        }

        $data = [];

        foreach ($chartData as $key => $value) {
            foreach ($value as $name => $count) {
                if ($name) {
                    $data[$key][] = [
                        'name' => $name,
                        'count' => $count
                    ];
                }
            }
        }

        $data['total'] = count($appData);

        return $data;
    }

    /**
     * Fetches the download count in JSON format
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function fetchDownloadData()
    {
        $data = [];

        try {
            $connection = DriverManager::getConnection($this->factory->getParameter('joomla_database'));
        } catch (DBALException $exception) {
            $data['message'] = $this->get('translator')->trans('Could not establish database connection');

            return $this->sendJsonResponse($data, 500);
        }

        $query = $connection->createQueryBuilder()
            ->select('r.version', 'r.download_count')
            ->from($this->factory->getParameter('joomla_dbprefix') . 'mautic_releases', 'r');

        try {
            $results = $connection->fetchAll($query->getSQL());
        } catch (DBALException $exception) {
            $data['message'] = $this->get('translator')->trans('Could not retrieve download data');

            return $this->sendJsonResponse($data, 500);
        }

        $total = 0;
        $versions = [];

        foreach ($results as $result) {
            $versions[$result['version']] = $result['download_count'];
            $total += $result['download_count'];
        }

        $data['releases'] = $versions;
        $data['total'] = $total;

        return $this->sendJsonResponse($data);
    }
}
