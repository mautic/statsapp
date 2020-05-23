<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) Mautic Contributors. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace Mautic\StatsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stats table
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mautic\StatsBundle\Entity\Mautic3UpgradeStatsRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Mautic3UpgradeStats
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="instance_id", type="text")
     */
    private $instanceId;

    /**
     * @var string
     *
     * @ORM\Column(name="application", type="text")
     */
    private $application;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="text")
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="php_version", type="text", nullable=true)
     */
    private $phpVersion;

    /**
     * @var string
     *
     * @ORM\Column(name="db_driver", type="text", nullable=true)
     */
    private $dbDriver;

    /**
     * @var string
     *
     * @ORM\Column(name="db_version", type="text", nullable=true)
     */
    private $dbVersion;

    /**
     * @var string
     *
     * @ORM\Column(name="server_os", type="text", nullable=true)
     */
    private $serverOs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_updated", type="datetime", nullable=true)
     */
    private $lastUpdated;

    /**
     * @var string
     *
     * @ORM\Column(name="upgrade_status", type="text", nullable=true)
     */
    private $upgradeStatus;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateLastUpdatedTime()
    {
        // update the modified time
        $this->setLastUpdated(new \DateTime('now', new \DateTimeZone('UTC')));
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set instanceId
     *
     * @param string $instanceId
     *
     * @return Mautic3UpgradeStats
     */
    public function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;

        return $this;
    }

    /**
     * Get instanceId
     *
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * Set application
     *
     * @param string $application
     *
     * @return Mautic3UpgradeStats
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return string
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return Mautic3UpgradeStats
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set phpVersion
     *
     * @param string $phpVersion
     *
     * @return Mautic3UpgradeStats
     */
    public function setPhpVersion($phpVersion)
    {
        $this->phpVersion = $phpVersion;

        return $this;
    }

    /**
     * Get phpVersion
     *
     * @return string
     */
    public function getPhpVersion()
    {
        return $this->phpVersion;
    }

    /**
     * Set dbDriver
     *
     * @param string $dbDriver
     *
     * @return Mautic3UpgradeStats
     */
    public function setDbDriver($dbDriver)
    {
        $this->dbDriver = $dbDriver;

        return $this;
    }

    /**
     * Get dbDriver
     *
     * @return string
     */
    public function getDbDriver()
    {
        return $this->dbDriver;
    }

    /**
     * Set dbVersion
     *
     * @param string $dbVersion
     *
     * @return Mautic3UpgradeStats
     */
    public function setDbVersion($dbVersion)
    {
        $this->dbVersion = $dbVersion;

        return $this;
    }

    /**
     * Get dbVersion
     *
     * @return string
     */
    public function getDbVersion()
    {
        return $this->dbVersion;
    }

    /**
     * Set serverOs
     *
     * @param string $serverOs
     *
     * @return Mautic3UpgradeStats
     */
    public function setServerOs($serverOs)
    {
        $this->serverOs = $serverOs;

        return $this;
    }

    /**
     * Get serverOs
     *
     * @return string
     */
    public function getServerOs()
    {
        return $this->serverOs;
    }

    /**
     * Set lastUpdated
     *
     * @param string $serverOs
     *
     * @return Mautic3UpgradeStats
     */
    public function setLastUpdated(\DateTime $lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * Get lastUpdated
     *
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * Set upgradeStatus
     *
     * @param string $upgradeStatus
     *
     * @return Mautic3UpgradeStats
     */
    public function setUpgradeStatus($upgradeStatus)
    {
        $this->upgradeStatus = $upgradeStatus;

        return $this;
    }

    /**
     * Get upgradeStatus
     *
     * @return string
     */
    public function getUpgradeStatus()
    {
        return $this->upgradeStatus;
    }
}
