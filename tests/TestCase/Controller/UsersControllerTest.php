<?php
namespace App\Test\TestCase\Controller;

use App\Controller\UsersController;
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
        'app.users',
        'app.payments',
        'app.membership_levels'
    ];

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
        $this->get('/users/logout');
        $this->assertResponseOk();
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
        $this->get('/users/reset-password');
        $this->assertResponseOk();
    }

    public function testView()
    {
        $this->get('/users/view/1');
        $this->assertResponseOk();
    }
}
