<?php
namespace App\Test\TestCase\Controller;

use App\Controller\TagsController;
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
        'app.tags',
        'app.users',
        'app.payments',
        'app.membership_levels',
        'app.tags_users'
    ];

    /**
     * Test index method
     *
     * @return void
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
     */
    public function testView()
    {
        $this->get('/tags/view/lorem');
        $this->assertResponseOk();
    }
}
