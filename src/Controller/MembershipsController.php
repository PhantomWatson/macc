<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Memberships Controller
 *
 * @property \App\Model\Table\MembershipsTable $Memberships
 */
class MembershipsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['purchase']);
    }

    public function purchase($membershipLevelId = null)
    {
        // If user is not logged in, give them a friendly "create an account or log in" page
        // Make sure registration / login page redirects back to this page
    }

    public function purchaseComplete()
    {
        $this->set('pageTitle', 'Membership Purchased!');
    }

    public function myMembership()
    {
        $userId = $this->Auth->user('id');
        $this->loadModel('Users');
        $user = $this->Users->find('all')
            ->where(['id' => $userId])
            ->contain([
                // Just the most recently-purchased membership
                'Memberships' => function ($q) {
                    return $q
                        ->contain(['MembershipLevels'])
                        ->limit(1)
                        ->order(['Memberships.created' => 'DESC']);
                }
            ])
            ->first();

        $this->set([
            'pageTitle' => 'My Membership Info',
            'user' => $user
        ]);
    }
}
