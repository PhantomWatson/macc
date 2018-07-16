<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PicturesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PicturesTable Test Case
 */
class PicturesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PicturesTable
     */
    public $Pictures;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.pictures',
        'app.users',
        'app.payments',
        'app.membership_levels',
        'app.memberships',
        'app.tags',
        'app.tags_users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Pictures = TableRegistry::getTableLocator()->get('Pictures');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Pictures);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
