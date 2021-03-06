<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * DonationsControllerTest class
 */
class DonationsControllerTest extends TestCase
{
    use IntegrationTestTrait;

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

    public function testDonate()
    {
        /*
        $this->get([
            'controller' => 'Donations',
            'action' => 'donate',
            '_ssl' => true
        ]);
        $this->assertResponseOk();
        */
        $this->markTestIncomplete('Need to set up self-signed certificate on localhost');
    }
}
