<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsAppBundle\Model;

use StatsAppBundle\Entity\Stats;

/**
 * Class StatsModel
 */
class StatsModel extends BaseModel
{

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

        $repo = $this->getRepository();

        $entity = $repo->findOneBy(['instanceId' => $instanceId, 'application' => $application]);

        return ($entity === null) ? new Stats() : $entity;
    }

    /**
     * {@inheritdoc}
     *
     * @return \StatsAppBundle\Entity\StatsRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('StatsAppBundle:Stats');
    }
}
