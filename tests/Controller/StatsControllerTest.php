<?php
/**
 * Stats Gathering Application
 *
 * @copyright  Copyright (C) WebSpark, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License Version 3
 */

namespace Mautic\StatsBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Test class for \Mautic\StatsBundle\Controller\StatsController
 */
class StatsController extends WebTestCase
{
    public function testLegacyGetDataActionDefaultBehavior()
    {
        $fixtures = ['Mautic\StatsBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/data');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('phpVersion', $response);
    }

    public function testLegacyGetDataActionSingleSource()
    {
        $fixtures = ['Mautic\StatsBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/data?source=phpVersion');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('phpVersion', $response);
    }

    public function testGetDataActionDefaultBehavior()
    {
        $fixtures = ['Mautic\StatsBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/all');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('phpVersion', $response);
    }

    public function testGetM3UpgradeDataActionDefaultBehavior()
    {
        $fixtures = ['Mautic\StatsBundle\Tests\Fixtures\LoadM3UpgradeStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/m3upgradejson');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('phpVersion', $response);
        $this->assertArrayHasKey('upgradeStatus', $response);
        $this->assertArrayHasKey('errorCode', $response);
    }

    public function testGetDataActionSingleSource()
    {
        $fixtures = ['Mautic\StatsBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/phpVersion');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey(PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION, $response['phpVersion']);
    }

    public function testGetDataActionSingleSourceWithAuthorizationHeader()
    {
        $fixtures = ['Mautic\StatsBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/phpVersion', [], [], ['HTTP_MAUTIC_RAW' => $this->getContainer()->getParameter('mautic_raw_header')]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(['phpVersion' => [['name' => PHP_VERSION, 'count' => 1]], 'total' => 1], $response);
    }

    public function testLegacySendAction()
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

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertSame('Data saved successfully', $response['message']);
    }

    public function testSendAction()
    {
        $params = [
            'application' => 'Mautic',
            'version' => '1.0.0',
            'instanceId' => 'a1b2c3d4'
        ];

        $client = static::createClient();

        $crawler = $client->request('POST', '/', $params);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertSame('Data saved successfully', $response['message']);
    }

    public function testLegacySendActionForMissingData()
    {
        $params = [
            'version' => '1.0.0',
            'instanceId' => 'a1b2c3d4'
        ];

        $client = static::createClient();

        $crawler = $client->request('POST', '/send', $params);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertSame('Missing data from the POST request', $response['message']);
    }

    public function testSendActionForMissingData()
    {
        $params = [
            'version' => '1.0.0',
            'instanceId' => 'a1b2c3d4'
        ];

        $client = static::createClient();

        $crawler = $client->request('POST', '/', $params);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertSame('Missing data from the POST request', $response['message']);
    }

    public function testLegacySendActionForUnsupportedApplications()
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

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertSame('The DoNotWork application is not supported', $response['message']);
    }

    public function testSendActionForUnsupportedApplications()
    {
        $params = [
            'application' => 'DoNotWork',
            'version' => '1.0.0',
            'instanceId' => 'a1b2c3d4'
        ];

        $client = static::createClient();

        $crawler = $client->request('POST', '/', $params);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertSame('The DoNotWork application is not supported', $response['message']);
    }

    public function testViewAction()
    {
        $fixtures = ['Mautic\StatsBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Application Statistics")')->count() > 0);
    }

    public function testViewM3UpgradeAction()
    {
        $fixtures = ['Mautic\StatsBundle\Tests\Fixtures\LoadM3UpgradeStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/m3upgrade');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Mautic 3 upgrade Statistics")')->count() > 0);
    }
}
