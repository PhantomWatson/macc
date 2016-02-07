<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MembershipLevelsTable;
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
        'app.membership_levels',
        'app.payments',
        'app.users',
        'app.membership_levels_users',
        'app.admin_adders',
        'app.refunders'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('MembershipLevels') ? [] : ['className' => 'App\Model\Table\MembershipLevelsTable'];
        $this->MembershipLevels = TableRegistry::get('MembershipLevels', $config);
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
