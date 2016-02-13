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
}
