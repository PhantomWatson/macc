<?php
namespace App\Test\TestCase\Command;

use Cake\Core\Configure;
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
        Configure::write(
            'EmailTransport.default',
            Configure::read('EmailTransport.Debug')
        );
    }

    /**
     * Tests that the command reports that it has sent an email to the appropriate user
     *
     * @return void
     */
    public function testSendAlertSuccess()
    {
        $this->exec('alert-upcoming-expirations');
        $this->assertOutputContains('Expiring memberships found:');
        $this->assertOutputContains('User with membership expiring tomorrow');
        $this->assertOutputContains('User with membership auto-renewing tomorrow');
        $this->assertOutputContains('Sent');
        $this->assertOutputNotContains('Exception');
        $this->assertOutputNotContains('Member User');
        $this->assertErrorEmpty();
    }
}
