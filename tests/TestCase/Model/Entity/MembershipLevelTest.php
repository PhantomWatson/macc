<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\MembershipLevel;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\MembershipLevel Test Case
 */
class MembershipLevelTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\MembershipLevel
     */
    public $MembershipLevel;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->MembershipLevel = new MembershipLevel();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MembershipLevel);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
