<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace StatsAppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Test class for \StatsApp\Controller\StatsController
 */
class StatsController extends WebTestCase
{
    public function testDataActionDefaultBehavior()
    {
        $fixtures = ['StatsAppBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/data');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }

    public function testDataActionSingleSource()
    {
        $fixtures = ['StatsAppBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/data?source=phpVersion');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }

    public function testSendAction()
    {
        $params = [
            'application' => 'Mautic',
            'version' => '1.0.0',
            'instanceId' => 'a1b2c3d4'
        ];

        $client = static::createClient();

        $crawler = $client->request('POST', '/send', $params);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }

    public function testSendActionForMissingData()
    {
        $params = [
            'version' => '1.0.0',
            'instanceId' => 'a1b2c3d4'
        ];

        $client = static::createClient();

        $crawler = $client->request('POST', '/send', $params);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }

    public function testSendActionForUnsupportedApplications()
    {
        $params = [
            'application' => 'DoNotWork',
            'version' => '1.0.0',
            'instanceId' => 'a1b2c3d4'
        ];

        $client = static::createClient();

        $crawler = $client->request('POST', '/send', $params);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
    }

    public function testViewAction()
    {
        $fixtures = ['StatsAppBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Application Statistics")')->count() > 0);
    }
}
