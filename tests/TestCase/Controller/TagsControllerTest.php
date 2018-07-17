<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\TagsController Test Case
 */
class TagsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.memberships',
        'app.tags',
        'app.tags_users',
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

    /**
     * Test index method
     *
     * @return void
     * @throws \PHPUnit\Exception
     */
    public function testIndex()
    {
        $this->get('/tags/index');
        $this->assertResponseOk();
    }

    /**
     * Test view method
     *
     * @return void
     * @throws \PHPUnit\Exception
     */
    public function testView()
    {
        $this->get('/tags/view/lorem');
        $this->assertResponseOk();
    }
}
