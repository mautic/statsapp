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
    public function testLegacyGetDataActionDefaultBehavior()
    {
        $fixtures = ['StatsAppBundle\Tests\Fixtures\LoadStatsData'];
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
        $fixtures = ['StatsAppBundle\Tests\Fixtures\LoadStatsData'];
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
        $fixtures = ['StatsAppBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/all');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('phpVersion', $response);
    }

    public function testGetDataActionSingleSource()
    {
        $fixtures = ['StatsAppBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/phpVersion');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('phpVersion', $response);
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
        $fixtures = ['StatsAppBundle\Tests\Fixtures\LoadStatsData'];
        $this->loadFixtures($fixtures);

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Application Statistics")')->count() > 0);
    }
}
