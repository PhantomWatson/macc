<?php
namespace App\Test\TestCase\Controller;

use App\Controller\Admin\PaymentsController;
use App\Test\Fixture\UsersFixture;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\Admin\PaymentsController Test Case
 */
class PaymentsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.membership_levels',
        'app.payments',
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
        $url = [
            'prefix' => 'admin',
            'controller' => 'Payments',
            'action' => 'index'
        ];

        // User not logged in
        $this->get($url);
        $this->assertRedirect([
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'login'
        ]);

        // Non-admin user
        $this->setUserSession();
        $this->get($url);
        $this->assertRedirect('/');

        // Admin
        $this->setAdminSession();
        $this->get($url);
        $this->assertResponseOk();
    }

    public function testAdd()
    {
        $url = [
            'prefix' => 'admin',
            'controller' => 'Payments',
            'action' => 'add'
        ];

        // User not logged in
        $this->get($url);
        $this->assertRedirect([
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'login'
        ]);

        // Non-admin user
        $this->setUserSession();
        $this->get($url);
        $this->assertRedirect('/');

        // Admin
        $this->setAdminSession();
        $this->get($url);
        $this->assertResponseOk();
    }
}
