<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Payment;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Payment Test Case
 */
class PaymentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Entity\Payment
     */
    public $Payment;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Payment = new Payment();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Payment);

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
