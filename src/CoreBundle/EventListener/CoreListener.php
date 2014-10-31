<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsApp\CoreBundle\EventListener;

use StatsApp\CoreBundle\Controller\BaseController;
use StatsApp\Factory;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class CoreListener
 */
class CoreListener
{

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Populates namespace, bundle, controller, and action into request to be used throughout application
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        // Only affect our controllers
        if ($controller[0] instanceof BaseController) {
            $request = $event->getRequest();

            // Set our objects into the controller
            $controller[0]->setRequest($request);
            $controller[0]->setFactory($this->factory);

            // Run any initialize functions
            $controller[0]->initialize($event);
        }
    }
}
