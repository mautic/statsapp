<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace Mautic\StatsBundle\Controller;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class StatsController
 */
class StatsController extends FOSRestController
{
    /**
     * Legacy handler for /data route requests
     *
     * @param Request $request
     *
     * @return Response
     */
    public function dataAction(Request $request)
    {
        $source = $request->query->get('source', 'all');

        // Proxy to the updated action
        return $this->getDataAction($request, $source);
    }

    /**
     * Retrieves the stat data as a JSON string
     *
     * @param Request $request
     * @param string $source The data source, defaults to 'all'
     *
     * @return Response
     *
     * @Get("/{source}", defaults={"source": "all"})
     */
    public function getDataAction(Request $request, $source = 'all')
    {
        $data = $this->fetchData($request, $source);

        // The downloads source may send back a message for an error condition instead of data so check for this
        if ($data instanceof Response) {
            return $data;
        }

        if ($source === 'downloads' && isset($data['message'])) {
            $view = $this->view($data, 500);

            return $this->handleView($view);
        }

        $view = $this->view($data, 200)
            ->setTemplate('MauticStatsBundle:Stats:data.html.twig')
            ->setTemplateData(
                [
                    'application' => 'Mautic',
                    'data' => $data
                ]
            );

        return $this->handleView($view);
    }

    /**
     * Receives the POSTed data from downstream applications
     *
     * @param Request $request
     *
     * @return Response
     *
     * @Post("/")
     */
    public function postDataAction(Request $request)
    {
        $data = [];

        // Fetch our data from the POST
        $postData = [
            'application' => $request->request->get('application', null),
            'version' => str_replace(['-dev', 'dev'], '', $request->request->get('version', null)),
            'phpVersion' => $request->request->get('phpVersion', null),
            'dbDriver' => $request->request->get('dbDriver', null),
            'dbVersion' => $request->request->get('dbVersion', null),
            'instanceId' => $request->request->get('instanceId', null),
            'serverOs' => $request->request->get('serverOs', null),
            'installSource' => $request->request->get('installSource', null)
        ];

        // Check for null values on the app, version, and instance; everything else we can do without
        if ($postData['application'] === null || $postData['version'] === null || $postData['instanceId'] === null) {
            $data['message'] = $this->get('translator')->trans('Missing data from the POST request');

            $view = $this->view($data, 500);

            return $this->handleView($view);
        }

        // Check if the application is supported
        $supported = $this->getParameter('supported_applications');

        if (!in_array($postData['application'], $supported)) {
            $data['message'] = $this->get('translator')->trans(
                'The %app% application is not supported',
                ['%app%' => $postData['application']]
            );

            $view = $this->view($data, 500);

            return $this->handleView($view);
        }

        /** @var \Mautic\StatsBundle\Model\StatsModel $model */
        $model = $this->get('mautic_stats.stats.model');

        $entity = $model->getEntity($postData['instanceId'], $postData['application']);

        // Loop over the post data and set it to the entity
        foreach ($postData as $key => $value) {
            $method = 'set'.ucwords($key);
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

        $view = $this->view($data, $code);

        return $this->handleView($view);
    }

    /**
     * Fetches the requested source data for the application
     *
     * @param Request $request
     * @param string $source
     *
     * @return array
     * @throws NotFoundHttpException
     */
    private function fetchData(Request $request, $source)
    {
        // If the source is downloads, then use our method that's specifically pulling this
        if ($source === 'downloads') {
            return $this->fetchDownloadData();
        }

        /** @var \Mautic\StatsBundle\Entity\StatsRepository $repo */
        $repo = $this->getDoctrine()->getRepository('MauticStatsBundle:Stats');
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
            'serverOs' => [],
            'installSource' => []
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

        // Filter our data into percentages unless authorized to receive raw data
        $authorizedRaw = $request->headers->has('Mautic-Raw') && $request->headers->get(
                'Mautic-Raw',
                'fail'
            ) === $this->getParameter('mautic_raw_header');
        $total = count($appData);

        if (!$authorizedRaw) {
            foreach ($data as $key => $dataGroup) {
                switch ($key) {
                    case 'phpVersion':
                        // We're going to group by minor version branch here and convert to a percentage
                        $counts = [];

                        foreach ($dataGroup as $row) {
                            $version = substr($row['name'], 0, 3);

                            // If the container does not exist, add it
                            if (!isset($counts[$version])) {
                                $counts[$version] = 0;
                            }

                            $counts[$version] += $row['count'];
                        }

                        $sanitizedData = [];

                        foreach ($counts as $version => $count) {
                            $sanitizedData[$version] = round($count / $total, 4) * 100;
                        }

                        $data[$key] = $sanitizedData;

                        break;

                    case 'serverOs':
                        // We're going to group by operating system here
                        $counts = [];

                        foreach ($dataGroup as $row) {
                            $fullOs = explode(' ', $row['name']);
                            $os = $fullOs[0];

                            if (!$os) {
                                $os = 'unknown';
                            }

                            // If the container does not exist, add it
                            if (!isset($counts[$os])) {
                                $counts[$os] = 0;
                            }

                            $counts[$os] += $row['count'];
                        }

                        $sanitizedData = [];

                        foreach ($counts as $os => $count) {
                            $sanitizedData[$os] = round($count / $total, 4) * 100;
                        }

                        $data[$key] = $sanitizedData;

                        break;

                    case 'dbDriver':
                    case 'version':
                    default:
                        // For now, group by the object name and figure out the percentages
                        $sanitizedData = [];

                        foreach ($dataGroup as $row) {
                            $sanitizedData[$row['name']] = round($row['count'] / $total, 4) * 100;
                        }

                        $data[$key] = $sanitizedData;

                        break;
                }
            }
        }

        $data['total'] = $total;

        // Check if returning a specific source
        if ($source !== 'all' && isset($data[$source])) {
            return [
                $source => $data[$source],
                'total' => $total
            ];
        }

        return $data;
    }

    /**
     * Fetches the download count based on collected stats in the download component
     *
     * @return array
     */
    private function fetchDownloadData()
    {
        $data = [];

        try {
            $connection = $this->get('doctrine.dbal.joomla_connection');
        } catch (DBALException $exception) {
            $data['message'] = $this->get('translator')->trans('Could not establish database connection');

            $view = $this->view($data, 500);

            return $this->handleView($view);
        }

        $query = $connection->createQueryBuilder()
            ->select('r.version', 'r.download_count')
            ->from($this->getParameter('joomla_dbprefix').'mautic_releases', 'r');

        try {
            $results = $connection->fetchAll($query->getSQL());
        } catch (DBALException $exception) {
            $data['message'] = $this->get('translator')->trans('Could not retrieve download data');

            $view = $this->view($data, 500);

            return $this->handleView($view);
        }

        $total = 0;
        $versions = [];

        foreach ($results as $result) {
            $versions[$result['version']] = $result['download_count'];
            $total += $result['download_count'];
        }

        $data['releases'] = $versions;
        $data['total'] = $total;

        return $data;
    }
}
