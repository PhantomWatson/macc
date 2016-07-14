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
    public $paginate = [
        'limit' => 25,
        'order' => [
            'Memberships.expires' => 'ASC'
        ],
        'sortWhitelist' => [
            'Users.name',
            'Memberships.expires',
            'Memberships.membership_level_id',
            'Memberships.auto_renew'
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    /**
     * List of current and expired memberships
     *
     * @return \Cake\Network\Response
     */
    public function index()
    {
        $usersTable = TableRegistry::get('Users');
        $query = $usersTable->find('members')
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
            ]);
        $members = $this->paginate($query);
            //->all();

        $this->set([
            'members' => $members,
            'pageTitle' => 'Memberships'
        ]);
    }
}
