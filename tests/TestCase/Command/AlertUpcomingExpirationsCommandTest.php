<?php
namespace App\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestCase;

/**
 * App\Command\AlertUpcomingExpirationsCommand Test Case
 */
class AlertUpcomingExpirationsCommandTest extends ConsoleIntegrationTestCase
{
    public $fixtures = [
        'app.memberships',
        'app.users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    /**
     *
     *
     * @return void
     */
    public function testSendAlertSuccess()
    {
        $this->exec('alert-upcoming-expirations');
        $this->assertOutputContains('Expiring memberships found:');
        $this->assertOutputContains('User with membership expiring tomorrow');
    }
}
