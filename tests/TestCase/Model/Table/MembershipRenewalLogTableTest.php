<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MembershipRenewalLogsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MembershipRenewalLogsTable Test Case
 */
class MembershipRenewalLogsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\MembershipRenewalLogsTable
     */
    public $MembershipRenewalLog;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.membership_renewal_log'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('MembershipRenewalLog') ? [] : ['className' => 'App\Model\Table\MembershipRenewalLogsTable'];
        $this->MembershipRenewalLog = TableRegistry::get('MembershipRenewalLog', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MembershipRenewalLog);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
