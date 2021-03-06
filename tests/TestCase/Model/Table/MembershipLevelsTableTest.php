<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MembershipLevelsTable Test Case
 */
class MembershipLevelsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\MembershipLevelsTable
     */
    public $MembershipLevels;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MembershipLevels',
        'app.Payments',
        'app.Users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->MembershipLevels = TableRegistry::getTableLocator()->get('MembershipLevels');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MembershipLevels);

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
