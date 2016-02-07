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

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $membershipLevels = $this->paginate($this->MembershipLevels);

        $this->set(compact('membershipLevels'));
        $this->set('_serialize', ['membershipLevels']);
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
        $membershipLevel = $this->MembershipLevels->get($id, [
            'contain' => ['Users', 'Payments']
        ]);

        $this->set('membershipLevel', $membershipLevel);
        $this->set('_serialize', ['membershipLevel']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
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
        $users = $this->MembershipLevels->Users->find('list', ['limit' => 200]);
        $this->set(compact('membershipLevel', 'users'));
        $this->set('_serialize', ['membershipLevel']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Membership Level id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
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

    /**
     * Delete method
     *
     * @param string|null $id Membership Level id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
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
