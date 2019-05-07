<?php
namespace App\Test\TestCase\Model\Table;

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
        'app.MembershipRenewalLog'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->MembershipRenewalLog = TableRegistry::getTableLocator()->get('MembershipRenewalLog');
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
