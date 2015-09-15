<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsAppBundle\Model;

use Doctrine\ORM\EntityManager;
use StatsAppBundle\Entity\Stats;

/**
 * Class StatsModel
 */
class StatsModel
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get a specific entity
     *
     * @return Stats
     */
    public function getEntity($instanceId = null, $application = null)
    {
        if ($instanceId === null && $application === null) {
            return new Stats();
        }

        $entity = $this->getRepository()->findOneBy(['instanceId' => $instanceId, 'application' => $application]);

        return ($entity === null) ? new Stats() : $entity;
    }

    /**
     * Retrieves an entity repository
     *
     * @return \StatsAppBundle\Entity\StatsRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('StatsAppBundle:Stats');
    }

    /**
     * Save an entity
     *
     * @param Stats $entity
     *
     * @return void
     */
    public function saveEntity($entity)
    {
        $this->getRepository()->saveEntity($entity);
    }
}
