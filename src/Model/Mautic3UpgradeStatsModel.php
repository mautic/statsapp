<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) Mautic contributors. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace Mautic\StatsBundle\Model;

use Doctrine\ORM\EntityManager;
use Mautic\StatsBundle\Entity\Mautic3UpgradeStats;
use Mautic\StatsBundle\Entity\Stats;

/**
 * Class Mautic3UpgradeStatsModel
 */
class Mautic3UpgradeStatsModel
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
     * @return Mautic3UpgradeStats
     */
    public function getEntity($instanceId = null, $application = null)
    {
        if ($instanceId === null && $application === null) {
            return new Mautic3UpgradeStats();
        }

        $entity = $this->getRepository()->findOneBy(['instanceId' => $instanceId, 'application' => $application]);

        return ($entity === null) ? new Mautic3UpgradeStats() : $entity;
    }

    /**
     * Retrieves an entity repository
     *
     * @return \Mautic\StatsBundle\Entity\Mautic3UpgradeStatsRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticStatsBundle:Mautic3UpgradeStats');
    }

    /**
     * Save an entity
     *
     * @param Mautic3UpgradeStats $entity
     *
     * @return void
     */
    public function saveEntity($entity)
    {
        $this->getRepository()->saveEntity($entity);
    }
}
