<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
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
        $query->select('s')
            ->where($query->expr()->eq('s.application', ':application'))
            ->setParameter('application', $application);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * Retrieves a list of applications and the number of installs in each
     *
     * @return array
     */
    public function getAppList()
    {
        $query = $this->createQueryBuilder($this->getTableAlias())
            ->select('s.application AS name, count(s.application) AS installs')
            ->groupBy('name');

        return $query->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
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
        $this->_em->persist($entity);

        if ($flush)
        {
            $this->_em->flush();
        }
    }
}
