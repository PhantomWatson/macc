<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\Table\MembershipsTable;
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
     * @return void
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
                            function ($exp) {
                                /** @var QueryExpression $exp */

                                return $exp->isNull('canceled');
                            }
                        ])
                        ->contain('MembershipLevels')
                        ->order(['expires' => 'DESC']);
                }
            ])
            ->orderAsc('name');
        $members = $this->paginate($query);

        $this->set([
            'members' => $members,
            'pageTitle' => 'Memberships'
        ]);
    }

    public function autoRenewalLogs()
    {
        $logsTable = TableRegistry::getTableLocator()->get('MembershipRenewalLogs');
        $query = $logsTable
            ->find()
            ->where(function (QueryExpression $exp) {
                return $exp->notEq('message', MembershipsTable::NO_RENEWAL_NEEDED_MSG);
            })
            ->orderDesc('created');
        $logs = $this->paginate($query);

        $this->set([
            'logs' => $logs,
            'pageTitle' => 'Membership Auto-Renewal Logs'
        ]);
    }

    /**
     * Lists the most recent expired memberships for users who are not current members
     * (i.e. who haven't renewed or upgraded their memberships)
     */
    public function expired()
    {
        $this->loadModel('Users');
        $users = $this->Users
            ->find('withUnrenewedMemberships')
            ->select(['id', 'name', 'email'])
            ->all();

        // Sort by expiration date
        $sortedUsers = [];
        foreach ($users as $user) {
            $key = $user->memberships[0]->expires->format('U') . '.' . $user->id;
            $sortedUsers[$key] = $user;
        }
        krsort($sortedUsers);

        $this->set([
            'pageTitle' => 'Expired Memberships',
            'users' => $sortedUsers
        ]);
    }
}
