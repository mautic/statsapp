<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) Mautic Contributors. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace Mautic\StatsBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Mautic\StatsBundle\Entity\Mautic3UpgradeStats;

/**
 * Class LoadM3UpgradeStatsData
 */
class LoadM3UpgradeStatsData extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $stats = new Mautic3UpgradeStats();
        $stats->setApplication('Mautic');
        $stats->setDbDriver('pdo_sqlite');
        $stats->setInstanceId('a1b2c3d4');
        $stats->setPhpVersion(PHP_VERSION);
        $stats->setServerOs(php_uname('s').' '.php_uname('r'));
        $stats->setVersion('1.0.0');
        $stats->setUpgradeStatus('failed');
        $stats->setErrorCode('ERR_TESTING');

        $manager->persist($stats);
        $manager->flush();
    }
}
