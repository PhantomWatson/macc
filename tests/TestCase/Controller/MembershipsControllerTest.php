<?php
namespace App\Test\TestCase\Controller;

use App\Controller\MembershipsController;
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
        'app.memberships',
        'app.users',
        'app.payments',
        'app.membership_levels',
        'app.tags',
        'app.tags_users'
    ];

    public function testLevels()
    {
        $this->get('/memberships/levels');
        $this->assertResponseOk();
    }
}
