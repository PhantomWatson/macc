<?php
namespace App\Test\TestCase\Controller;

use App\Controller\UsersController;
use App\Mailer\Mailer;
use App\Test\Fixture\UsersFixture;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\UsersController Test Case
 */
class UsersControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.memberships',
        'app.users'
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

    public function testForgotPassword()
    {
        $this->get('/users/forgot-password');
        $this->assertResponseOk();
    }

    public function testlogin()
    {
        $this->get('/users/login');
        $this->assertResponseOk();
    }

    public function testLogout()
    {
        $this->setNonMemberSession();
        $this->get('/users/logout');
        $this->assertSession(null, 'Auth.User.id');
        $this->assertRedirect('/');
    }

    public function testMembers()
    {
        $this->get('/users/members');
        $this->assertResponseOk();
    }

    public function testRegister()
    {
        $this->get('/users/register');
        $this->assertResponseOk();
    }

    public function testResetPassword()
    {
        $userId = 1;
        $timestamp = time();
        $hash = Mailer::getPasswordResetHash($userId, $timestamp);

        $this->get(Router::url([
            'controller' => 'Users',
            'action' => 'resetPassword',
            $userId,
            $timestamp,
            $hash
        ]));
        $this->assertResponseOk();
    }

    public function testViewMember()
    {
        $this->get([
            'controller' => 'Users',
            'action' => 'view',
            1,
            'test-user-1'
        ]);
        $this->assertResponseOk();
    }

    public function testViewNonMember()
    {
        $this->get([
            'controller' => 'Users',
            'action' => 'view',
            2,
            'test-user-2'
        ]);
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'members'
        ]);
    }

    public function testViewOwnMemberProfile()
    {
        $this->setMemberSession();
        $this->get([
            'controller' => 'Users',
            'action' => 'view',
            1,
            'test-user-1'
        ]);
        $this->assertResponseOk();
    }

    public function testViewOwnNonMemberProfile()
    {
        $this->setNonMemberSession();
        $this->get([
            'controller' => 'Users',
            'action' => 'view',
            2,
            'test-user-2'
        ]);
        $this->assertRedirectContains(Router::url([
            'controller' => 'Users',
            'action' => 'members'
        ]));
    }

    public function testAccountAuth()
    {
        $this->setNonMemberSession();
        $this->get([
            'controller' => 'Users',
            'action' => 'account'
        ]);
        $this->assertResponseOk();
    }

    public function testAccountUnauth()
    {
        $this->get([
            'controller' => 'Users',
            'action' => 'account'
        ]);
        $this->assertRedirectContains(Router::url([
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }

    public function testChangePasswordAuth()
    {
        $this->setNonMemberSession();
        $this->get([
            'controller' => 'Users',
            'action' => 'changePassword'
        ]);
        $this->assertResponseOk();
    }

    public function testChangePasswordUnauth()
    {
        $this->get([
            'controller' => 'Users',
            'action' => 'changePassword'
        ]);
        $this->assertRedirectContains(Router::url([
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }

    public function testEditProfileAuth()
    {
        $this->setNonMemberSession();
        $this->get([
            'controller' => 'Users',
            'action' => 'editProfile'
        ]);
        $this->assertResponseOk();
    }

    public function testEditProfileUnauth()
    {
        $this->get([
            'controller' => 'Users',
            'action' => 'editProfile'
        ]);
        $this->assertRedirectContains(Router::url([
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }
}
