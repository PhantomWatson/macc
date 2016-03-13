<?php
namespace App\Test\TestCase\Controller;

use App\Controller\Admin\MembershipLevelsController;
use App\Test\Fixture\UsersFixture;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\Admin\MembershipLevelsController Test Case
 */
class MembershipLevelsControllerTest extends IntegrationTestCase
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

    private function setUserSession()
    {
        $usersFixture = new UsersFixture();
        $this->session([
            'Auth' => [
                'User' => $usersFixture->records[0]
            ]
        ]);
    }

    private function setAdminSession()
    {
        $usersFixture = new UsersFixture();
        $this->session([
            'Auth' => [
                'User' => $usersFixture->records[2]
            ]
        ]);
    }

    public function testIndex()
    {
        // User not logged in
        $this->get([
            'prefix' => 'admin',
            'controller' => 'MembershipLevels',
            'action' => 'index'
        ]);
        $this->assertRedirect([
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'login'
        ]);

        // Non-admin user
        $this->setUserSession();
        $this->get([
            'prefix' => 'admin',
            'controller' => 'MembershipLevels',
            'action' => 'index'
        ]);
        $this->assertRedirect('/');

        // Admin
        $this->setAdminSession();
        $this->get([
            'prefix' => 'admin',
            'controller' => 'MembershipLevels',
            'action' => 'index'
        ]);
        $this->assertResponseOk();
    }
}
