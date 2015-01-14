<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsAppBundle\Model;

use StatsAppBundle\Factory;

/**
 * Class BaseModel
 */
abstract class BaseModel
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
        $this->em      = $factory->getEntityManager();
    }

    /**
     * Retrieves an entity repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    abstract public function getRepository();

    /**
     * Return list of entities
     *
     * @param array $args [start, limit, filter, orderBy, orderByDir]
     *
     * @return mixed
     */
    public function getEntities(array $args = array())
    {
        return $this->getRepository()->getEntities($args);
    }

    /**
     * Save an entity
     *
     * @param object $entity
     *
     * @return void
     */
    public function saveEntity($entity)
    {
        $this->getRepository()->saveEntity($entity);
    }

    /**
     * Save an array of entities
     *
     * @param array $entities
     * @param bool  $unlock
     *
     * @return void
     */
    public function saveEntities($entities, $unlock = true)
    {
        // Iterate over the results so the events are dispatched on each delete
        $batchSize = 20;

        foreach ($entities as $k => $entity) {
            $this->getRepository()->saveEntity($entity, false);

            if ((($k + 1) % $batchSize) === 0) {
                $this->em->flush();
            }
        }

        $this->em->flush();
    }
}
