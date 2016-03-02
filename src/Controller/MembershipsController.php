<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Network\Exception\BadRequestException;

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
        $membership = $this->Memberships->getCurrentMembership($userId);

        $this->set([
            'membership' => $membership,
            'pageTitle' => 'My Membership Info'
        ]);
    }

    public function toggleAutoRenewal($value = null)
    {
        if (! in_array($value, ['1', '0'])) {
            throw new BadRequestException('Invalid value supplied');
        }

        $userId = $this->Auth->user('id');
        $membership = $this->Memberships->getCurrentMembership($userId);
        $membershipEntity = $this->Memberships->get($membership['id']);
        $membershipEntity = $this->Memberships->patchEntity($membershipEntity, [
            'auto_renew' => $value
        ]);
        $this->Memberships->save($membershipEntity);
        $msg = 'Membership auto-renewal turned '.($value ? 'on' : 'off').'.';
        if ($value) {
            $timestamp = $membership->expires->format('U') - (60 * 60 * 24);
            $msg .= ' Your membership will be automatically renewed on '.date('F j, Y', $timestamp).'.';
        }
        $this->Flash->success($msg);
        $this->redirect($this->referer());
    }
}
