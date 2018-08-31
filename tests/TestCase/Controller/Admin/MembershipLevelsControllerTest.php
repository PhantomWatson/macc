<?php

namespace App\Test\TestCase\Controller\Admin;

use App\Test\Fixture\UsersFixture;
use Cake\Routing\Router;
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
        'app.logos',
        'app.membership_levels',
        'app.memberships',
        'app.users'
    ];

    private $indexUrl = [
        'prefix' => 'admin',
        'controller' => 'MembershipLevels',
        'action' => 'index'
    ];
    private $addUrl = [
        'prefix' => 'admin',
        'controller' => 'MembershipLevels',
        'action' => 'add'
    ];
    private $editUrl = [
        'prefix' => 'admin',
        'controller' => 'MembershipLevels',
        'action' => 'edit',
        1
    ];
    private $deleteUrl = [
        'prefix' => 'admin',
        'controller' => 'MembershipLevels',
        'action' => 'delete',
        1
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

    /**
     * Tests that the index page cannot be accessed by anonymous users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testIndexFailNotLoggedIn()
    {
        $this->get($this->indexUrl);
        $this->assertRedirectContains(Router::url([
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }

    /**
     * Tests that the index page cannot be accessed by non-admin users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testIndexFailNotAdmin()
    {
        $this->setUserSession();
        $this->get($this->indexUrl);
        $this->assertRedirect('/');
    }

    /**
     * Tests that the index page can be accessed by admin users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testIndexSuccess()
    {
        $this->setAdminSession();
        $this->get($this->indexUrl);
        //print_r($this->_response->getBody()->__toString());
        $this->assertResponseOk();
    }

    /**
     * Tests that the add page cannot be accessed by anonymous users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testAddFailNotLoggedIn()
    {
        $this->get($this->addUrl);
        $this->assertRedirectContains(Router::url([
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }

    /**
     * Tests that the add page cannot be accessed by non-admin users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testAddFailNotAdmin()
    {
        $this->setUserSession();
        $this->get($this->addUrl);
        $this->assertRedirect('/');
    }

    /**
     * Tests that the add page can be accessed by admin users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testAddViewSuccess()
    {
        $this->setAdminSession();
        $this->get($this->addUrl);
        $this->assertResponseOk();
    }

    /**
     * Tests that the edit page cannot be accessed by anonymous users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testEditFailNotLoggedIn()
    {
        $this->get($this->editUrl);
        $this->assertRedirectContains(Router::url([
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }

    /**
     * Tests that the edit page cannot be accessed by non-admin users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testEditFailNotAdmin()
    {
        $this->setUserSession();
        $this->get($this->editUrl);
        $this->assertRedirect('/');
    }

    /**
     * Tests that the edit page can be accessed by admin users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testEditViewSuccess()
    {
        $this->setAdminSession();
        $this->get($this->editUrl);
        $this->assertResponseOk();
    }

    /**
     * Tests that the delete page cannot be accessed by anonymous users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testDeleteFailNotLoggedIn()
    {
        $this->post($this->deleteUrl);
        $this->assertRedirectContains(Router::url([
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }

    /**
     * Tests that the delete page cannot be accessed by non-admin users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testDeleteFailNotAdmin()
    {
        $this->setUserSession();
        $this->post($this->deleteUrl);
        $this->assertRedirect('/');
    }

    /**
     * Tests that the delete page can be accessed by admin users
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testDeleteSuccess()
    {
        $this->setAdminSession();
        $this->post($this->deleteUrl);
        $this->assertRedirect([
            'prefix' => 'admin',
            'controller' => 'MembershipLevels',
            'action' => 'index'
        ]);
    }
}
