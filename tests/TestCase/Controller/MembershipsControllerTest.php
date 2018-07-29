<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\UsersFixture;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\MembershipsController Test Case
 */
class MembershipsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.membership_levels',
        'app.memberships',
        'app.payments'
    ];

    /**
     * Sets up this set of tests
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->configRequest([
            'environment' => [
                'HTTPS' => 'on'
            ]
        ]);
    }

    public function setMemberSession()
    {
        $usersFixture = new UsersFixture();
        $this->session([
            'Auth' => [
                'User' => $usersFixture->records[0]
            ]
        ]);
    }

    public function setNonMemberSession()
    {
        $usersFixture = new UsersFixture();
        $this->session([
            'Auth' => [
                'User' => $usersFixture->records[1]
            ]
        ]);
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testLevels()
    {
        $this->get('/memberships/levels');
        $this->assertResponseOk();
    }

    /**
     * Tests that a membership level page can be viewed
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testLevelViewSuccess()
    {
        $this->get([
            'controller' => 'Memberships',
            'action' => 'level',
            1
        ]);
        $this->assertResponseOk();
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testMyMembershipAuth()
    {
        $this->setNonMemberSession();
        $this->get([
            'controller' => 'Memberships',
            'action' => 'myMembership',
            1
        ]);
        $this->assertResponseOk();
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testMyMembershipUnauth()
    {
        $this->get([
            'controller' => 'Memberships',
            'action' => 'myMembership',
            1
        ]);
        $this->assertRedirectContains(Router::url([
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testPurchaseCompleteAuth()
    {
        $this->setNonMemberSession();
        $this->get([
            'controller' => 'Memberships',
            'action' => 'purchaseComplete'
        ]);
        $this->assertResponseOk();
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testPurchaseCompleteUnauth()
    {
        $this->get([
            'controller' => 'Memberships',
            'action' => 'purchaseComplete'
        ]);
        $this->assertRedirectContains(Router::url([
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }

    /**
     * Tests that auto renewal can be toggled on
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testToggleAutoRenewalOnSuccess()
    {
        $this->setMemberSession();

        $this->post([
            'controller' => 'Memberships',
            'action' => 'toggleAutoRenewal',
            1
        ]);
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');
        $result = $membershipsTable->get(1)->auto_renew;
        $this->assertEquals(true, $result);
    }

    /**
     * Tests that auto renewal can be toggled off
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testToggleAutoRenewalOffSuccess()
    {
        $this->setMemberSession();

        $this->post([
            'controller' => 'Memberships',
            'action' => 'toggleAutoRenewal',
            0
        ]);
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');
        $result = $membershipsTable->get(1)->auto_renew;
        $this->assertEquals(false, $result);
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testToggleAutoRenewalUnauth()
    {
        $this->get([
            'controller' => 'Memberships',
            'action' => 'toggleAutoRenewal',
            1
        ]);
        $this->assertRedirectContains(Router::url([
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }
}
