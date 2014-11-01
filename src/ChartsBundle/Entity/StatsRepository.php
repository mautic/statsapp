<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) 2014 WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsApp\ChartsBundle\Entity;

use StatsApp\CoreBundle\Entity\BaseRepository;

/**
 * Class StatsRepository
 */
class StatsRepository extends BaseRepository
{
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
}
