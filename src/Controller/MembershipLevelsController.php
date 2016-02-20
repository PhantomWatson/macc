<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MembershipLevels Controller
 *
 * @property \App\Model\Table\MembershipLevelsTable $MembershipLevels
 */
class MembershipLevelsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['index']);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $membershipLevels = $this->MembershipLevels
            ->find('all')
            ->order(['cost' => 'ASC']);

        $this->set([
            'membershipLevels' => $membershipLevels,
            'pageTitle' => 'Become a Member'
        ]);
    }

    /**
     * View method
     *
     * @param string|null $id Membership Level id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $membershipLevel = $this->MembershipLevels->get($id);
        $this->set([
            'membershipLevel' => $membershipLevel,
            'pageTitle' => 'Purchase "'.$membershipLevel->name.'" Membership'
        ]);
    }
}
