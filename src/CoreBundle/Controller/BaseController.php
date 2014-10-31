<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic, NP. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace StatsApp\CoreBundle\Controller;

use StatsApp\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class BaseController
 */
class BaseController extends Controller
{

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Sends a JSON response
     *
     * @param array $data The response data
     * @param int   $code The response status code
     *
     * @return JsonResponse
     */
    protected function sendJsonResponse(array $data, $code)
    {
        $response = new JsonResponse($data, $code);
        $response->headers->set('Content-Length', strlen($response->getContent()));
        return $response;
    }

    /**
     * @param Factory $factory
     *
     * @return void
     */
    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param Request $request
     *
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @return void
     */
    public function initialize(FilterControllerEvent $event)
    {
    }
}
