<?php
namespace App\Test\TestCase\Controller;

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
        'app.Logos',
        'app.MembershipLevels',
        'app.Memberships',
        'app.Users'
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
    public function testForgotPassword()
    {
        $this->get('/users/forgot-password');
        $this->assertResponseOk();
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testlogin()
    {
        $this->get('/users/login');
        $this->assertResponseOk();
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testLogout()
    {
        $this->setNonMemberSession();
        $this->get('/users/logout');
        $this->assertSession(null, 'Auth.User.id');
        $this->assertRedirect('/');
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testMembers()
    {
        $this->get('/users/members');
        $this->assertResponseOk();
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testRegister()
    {
        $this->get('/users/register');
        $this->assertResponseOk();
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
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

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
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

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
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

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
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

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
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

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testAccountAuth()
    {
        $this->setNonMemberSession();
        $this->get([
            'controller' => 'Users',
            'action' => 'myContact'
        ]);
        $this->assertResponseOk();
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testAccountUnauth()
    {
        $this->get([
            'controller' => 'Users',
            'action' => 'myContact'
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
    public function testChangePasswordAuth()
    {
        $this->setNonMemberSession();
        $this->get([
            'controller' => 'Users',
            'action' => 'changePassword'
        ]);
        $this->assertResponseOk();
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
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

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testMyBioMember()
    {
        $this->setMemberSession();
        $this->get([
            'controller' => 'Users',
            'action' => 'myBio'
        ]);
        $this->assertResponseOk();
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testMyBioUnauth()
    {
        $this->get([
            'controller' => 'Users',
            'action' => 'myBio'
        ]);
        $this->assertRedirectContains(Router::url([
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }
}
