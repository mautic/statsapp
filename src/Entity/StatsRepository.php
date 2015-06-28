<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsAppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class StatsRepository
 */
class StatsRepository extends EntityRepository
{
    /**
     * Retrieves all installation data for a given application
     *
     * @return array
     */
    public function getAppData($application)
    {
        $query = $this->createQueryBuilder($this->getTableAlias());
        $query->where($query->expr()->eq($this->getTableAlias().'.application', ':application'))
            ->setParameter('application', $application);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retrieve the alias for the entity
     *
     * @return string
     */
    public function getTableAlias()
    {
        return 's';
    }

    /**
     * Save an entity through the repository
     *
     * @param object $entity
     * @param bool   $flush true by default; use false if persisting in batches
     *
     * @return void
     */
    public function saveEntity($entity, $flush = true)
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
