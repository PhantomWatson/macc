<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * MembershipLevels Controller
 *
 * @property \App\Model\Table\MembershipLevelsTable $MembershipLevels
 */
class MembershipLevelsController extends AppController
{
    public function index()
    {
        $membershipLevels = $this->MembershipLevels
            ->find('all')
            ->order(['cost' => 'ASC']);

        $this->set([
            'membershipLevels' => $membershipLevels,
            'pageTitle' => 'Membership Levels'
        ]);
    }

    public function add()
    {
        $membershipLevel = $this->MembershipLevels->newEntity();
        if ($this->request->is('post')) {
            $membershipLevel = $this->MembershipLevels->patchEntity($membershipLevel, $this->request->data);
            if ($this->MembershipLevels->save($membershipLevel)) {
                $this->Flash->success(__('The membership level has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The membership level could not be saved. Please, try again.'));
            }
        }
        $this->set([
            'membershipLevel' => $membershipLevel,
            'pageTitle' => 'Add New Membership Level'
        ]);
    }

    public function edit($id = null)
    {
        $membershipLevel = $this->MembershipLevels->get($id, [
            'contain' => ['Users']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $membershipLevel = $this->MembershipLevels->patchEntity($membershipLevel, $this->request->data);
            if ($this->MembershipLevels->save($membershipLevel)) {
                $this->Flash->success(__('The membership level has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The membership level could not be saved. Please, try again.'));
            }
        }
        $users = $this->MembershipLevels->Users->find('list', ['limit' => 200]);
        $this->set(compact('membershipLevel', 'users'));
        $this->set('_serialize', ['membershipLevel']);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $membershipLevel = $this->MembershipLevels->get($id);
        if ($this->MembershipLevels->delete($membershipLevel)) {
            $this->Flash->success(__('The membership level has been deleted.'));
        } else {
            $this->Flash->error(__('The membership level could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
