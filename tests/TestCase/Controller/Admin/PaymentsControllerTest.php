<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Test\Fixture\UsersFixture;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Admin\PaymentsController Test Case
 */
class PaymentsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MembershipLevels',
        'app.Payments',
        'app.Users'
    ];

    private $indexUrl = [
        'prefix' => 'admin',
        'controller' => 'Payments',
        'action' => 'index'
    ];
    private $addUrl = [
        'prefix' => 'admin',
        'controller' => 'Payments',
        'action' => 'add'
    ];
    private $refundUrl = [
        'prefix' => 'admin',
        'controller' => 'Payments',
        'action' => 'refund',
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
     * Tests that the index page cannot be viewed by an anonymous user
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
     * Tests that the index page cannot be viewed by a non-admin
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
     * Tests that the index page can be viewed by an admin
     *
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testIndexSuccess()
    {
        $this->setAdminSession();
        $this->get($this->indexUrl);
        $this->assertResponseOk();
    }

    /**
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
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testRefundFailNotLoggedIn()
    {
        $this->post($this->refundUrl);
        $this->assertRedirectContains(Router::url([
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'login'
        ]));
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testRefundFailNotAdmin()
    {
        $this->markTestIncomplete();
        /*$this->setUserSession();
        $this->post($this->refundUrl);
        $this->assertRedirect('/');*/
    }

    /**
     * @throws \PHPUnit\Exception
     * @return void
     */
    public function testRefundSuccess()
    {
        $this->markTestIncomplete();
        /*
        $this->setAdminSession();
        $this->post($this->refundUrl);
        $this->assertRedirect([
            'prefix' => 'admin',
            'controller' => 'Payments',
            'action' => 'index'
        ]);
        $paymentsTable = TableRegistry::get('Payments');
        $payment = $paymentsTable->get(1);
        $this->assertNotEquals($payment->refunded, null);*/
    }
}
