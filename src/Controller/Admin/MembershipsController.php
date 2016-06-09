<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Memberships Controller (admin)
 *
 * @property \App\Model\Table\MembershipsTable $Memberships
 */
class MembershipsController extends AppController
{
    /**
     * List of current and expired memberships
     *
     * @return \Cake\Network\Response
     */
    public function index()
    {
        $usersTable = TableRegistry::get('Users');
        $members = $usersTable->find('members')
            ->select(['id', 'name', 'slug'])
            ->contain([
                'Memberships' => function ($q) {
                    return $q
                        ->where([
                            function ($exp, $q) {
                                return $exp->isNull('canceled');
                            }
                        ])
                        ->contain('MembershipLevels')
                        ->order(['expires' => 'DESC']);
                }
            ])
            ->order(['Users.name' => 'ASC'])
            ->all();

        $this->set([
            'members' => $members,
            'pageTitle' => 'Memberships'
        ]);
    }
}
