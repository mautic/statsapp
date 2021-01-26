<?php

declare(strict_types=1);

/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace Mautic\StatsBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Mautic\StatsBundle\Entity\Stats;

/**
 * Class LoadStatsData
 */
class LoadStatsData extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $stats = new Stats();
        $stats->setApplication('Mautic');
        $stats->setDbDriver('pdo_sqlite');
        $stats->setInstanceId('a1b2c3d4');
        $stats->setPhpVersion(PHP_VERSION);
        $stats->setServerOs(php_uname('s').' '.php_uname('r'));
        $stats->setVersion('1.0.0');

        $manager->persist($stats);
        $manager->flush();
    }
}
