<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsApp;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Application factory class
 */
class Factory
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Retrieves Doctrine EntityManager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * Get the current environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->container->getParameter('kernel.environment');
    }

    /**
     * Retrieves the specified model object
     *
     * @param $name
     *
     * @return object
     * @throws NotAcceptableHttpException
     */
    public function getModel($name)
    {
        static $models = array();

        // Shortcut for models with same name as bundle
        if (strpos($name, '.') === false) {
            $name = "$name.$name";
        }

        if (!array_key_exists($name, $models)) {
            $parts = explode('.', $name);
            if (count($parts) !== 2) {
                throw new NotAcceptableHttpException($name . ' is not an acceptable model name.');
            }

            $modelClass = '\\StatsApp\\' . ucfirst($parts[0]) . 'Bundle\\Model\\' . ucfirst($parts[1]) . 'Model';

            if (!class_exists($modelClass)) {
                throw new NotAcceptableHttpException($name . ' is not an acceptable model name.');
            }

            $models[$name] = new $modelClass($this);

            if (method_exists($models[$name], 'initialize')) {
                $models[$name]->initialize();
            }
        }

        return $models[$name];
    }

    /**
     * Get a parameter from the container
     *
     * @return string
     */
    public function getParameter($key)
    {
        return $this->container->getParameter('supported_applications');
    }
}
