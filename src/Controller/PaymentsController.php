<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Payments Controller
 *
 * @property \App\Model\Table\PaymentsTable $Payments
 */
class PaymentsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'MembershipLevels', 'AdminAdders', 'Refunders']
        ];
        $payments = $this->paginate($this->Payments);

        $this->set(compact('payments'));
        $this->set('_serialize', ['payments']);
    }

    /**
     * View method
     *
     * @param string|null $id Payment id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $payment = $this->Payments->get($id, [
            'contain' => ['Users', 'MembershipLevels', 'AdminAdders', 'Refunders', 'MembershipLevelsUsers']
        ]);

        $this->set('payment', $payment);
        $this->set('_serialize', ['payment']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $payment = $this->Payments->newEntity();
        if ($this->request->is('post')) {
            $payment = $this->Payments->patchEntity($payment, $this->request->data);
            if ($this->Payments->save($payment)) {
                $this->Flash->success(__('The payment has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The payment could not be saved. Please, try again.'));
            }
        }
        $users = $this->Payments->Users->find('list', ['limit' => 200]);
        $membershipLevels = $this->Payments->MembershipLevels->find('list', ['limit' => 200]);
        $adminAdders = $this->Payments->AdminAdders->find('list', ['limit' => 200]);
        $refunders = $this->Payments->Refunders->find('list', ['limit' => 200]);
        $this->set(compact('payment', 'users', 'membershipLevels', 'adminAdders', 'refunders'));
        $this->set('_serialize', ['payment']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Payment id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $payment = $this->Payments->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $payment = $this->Payments->patchEntity($payment, $this->request->data);
            if ($this->Payments->save($payment)) {
                $this->Flash->success(__('The payment has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The payment could not be saved. Please, try again.'));
            }
        }
        $users = $this->Payments->Users->find('list', ['limit' => 200]);
        $membershipLevels = $this->Payments->MembershipLevels->find('list', ['limit' => 200]);
        $adminAdders = $this->Payments->AdminAdders->find('list', ['limit' => 200]);
        $refunders = $this->Payments->Refunders->find('list', ['limit' => 200]);
        $this->set(compact('payment', 'users', 'membershipLevels', 'adminAdders', 'refunders'));
        $this->set('_serialize', ['payment']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Payment id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $payment = $this->Payments->get($id);
        if ($this->Payments->delete($payment)) {
            $this->Flash->success(__('The payment has been deleted.'));
        } else {
            $this->Flash->error(__('The payment could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
