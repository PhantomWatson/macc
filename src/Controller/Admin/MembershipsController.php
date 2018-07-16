<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

/**
 * Memberships Controller (admin)
 *
 * @property \App\Model\Table\MembershipsTable $Memberships
 */
class MembershipsController extends AppController
{
    public $paginate = [
        'Memberships' => [
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
        ],
        'MembershipRenewalLogs' => [
            'limit' => 25,
            'order' => [
                'MembershipRenewalLogs.created' => 'DESC'
            ],
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
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $query = $usersTable->find('members')
            ->select(['id', 'name', 'slug'])
            ->contain([
                'Memberships' => function ($q) {
                    /** @var Query $q */

                    return $q
                        ->where([
                            function ($exp, $q) {
                                /** @var QueryExpression $exp */

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

    public function autoRenewalLogs()
    {
        $logsTable = TableRegistry::getTableLocator()->get('MembershipRenewalLogs');
        $logs = $this->paginate($logsTable);

        $this->set([
            'logs' => $logs,
            'pageTitle' => 'Membership Auto-Renewal Logs'
        ]);
    }
}
