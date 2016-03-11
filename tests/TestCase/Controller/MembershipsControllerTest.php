<?php
namespace App\Test\TestCase\Controller;

use App\Controller\MembershipsController;
use App\Test\Fixture\UsersFixture;
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
        'app.users'
    ];

    public function setNonMemberSession()
    {
        $usersFixture = new UsersFixture();
        $this->session([
            'Auth' => [
                'User' => $usersFixture->records[1]
            ]
        ]);
    }

    public function testLevels()
    {
        $this->get('/memberships/levels');
        $this->assertResponseOk();
    }

    public function testLevelUnauth()
    {
        $this->get([
            'controller' => 'Memberships',
            'action' => 'level',
            1
        ]);
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'login'
        ]);
    }

    public function testLevelAuth()
    {
        $this->setNonMemberSession();
        $this->get([
            'controller' => 'Memberships',
            'action' => 'level',
            1
        ]);
        $this->assertResponseOk();
    }

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

    public function testMyMembershipUnauth()
    {
        $this->get([
            'controller' => 'Memberships',
            'action' => 'myMembership',
            1
        ]);
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'login'
        ]);
    }

    public function testPurchaseCompleteAuth()
    {
        $this->setNonMemberSession();
        $this->get([
            'controller' => 'Memberships',
            'action' => 'purchaseComplete'
        ]);
        $this->assertResponseOk();
    }

    public function testPurchaseCompleteUnauth()
    {
        $this->get([
            'controller' => 'Memberships',
            'action' => 'purchaseComplete'
        ]);
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'login'
        ]);
    }
}
