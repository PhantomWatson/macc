<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\I18n\Time;

/**
 * Payments Controller
 *
 * @property \App\Model\Table\PaymentsTable $Payments
 */
class PaymentsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow([
            'completeDonation',
            'completePurchase',
            'donate',
            'donationComplete'
        ]);
    }

    public function completePurchase()
    {
        $this->viewBuilder()->layout('json');
        $this->set('_serialize', true);

        // Verify user
        $userId = $this->request->data('userId');
        $this->loadModel('Users');
        try {
            $user = $this->Users->get($userId);
        } catch (RecordNotFoundException $e) {
            $this->set('retval', [
                'success' => false,
                'message' => "Sorry, but that user account ('$userId') was not found."
            ]);
            $this->response->statusCode('404');
            return $this->render();
        }

        // Verify membership level
        $membershipLevelId = $this->request->data('membershipLevelId');
        $this->loadModel('MembershipLevels');
        try {
            $membershipLevel = $this->MembershipLevels->get($membershipLevelId);
        } catch (RecordNotFoundException $e) {
            $this->set('retval', [
                'success' => false,
                'message' => "Sorry, but that membership level ('$membershipLevelId') was not found."
            ]);
            $this->response->statusCode('404');
            return $this->render();
        }

        // Save payment
        $payment = $this->Payments->newEntity([
            'user_id' => $userId,
            'membership_level_id' => $membershipLevelId,
            'amount' => $membershipLevel->cost
        ]);
        $errors = $payment->errors();
        if (empty($errors)) {
            $payment = $this->Payments->save($payment);

            // Save membership
            $this->loadModel('Memberships');
            $membership = $this->Memberships->newEntity([
                'user_id' => $userId,
                'membership_level_id' => $membershipLevelId,
                'payment_id' => $payment->id,
                'recurring_billing' => $this->request->data('recurringBilling'),
                'expires' => new Time(strtotime('+1 year'))
            ]);
            $errors = $membership->errors();
            if (empty($errors)) {
                $membership = $this->Memberships->save($membership);
                $this->set('retval', [
                    'success' => true,
                    'message' => 'Purchase completed!'
                ]);
                return $this->render();
            }
        }

        $adminEmail = Configure::read('admin_email');
        $msg = 'There was an error processing your payment. ';
        $msg .= 'For assistance, please contact <a href="mailto:'.$adminEmail.'">'.$adminEmail.'</a>. ';
        $this->set('retval', [
            'success' => false,
            'message' => $msg
        ]);
        $this->response->statusCode('500');
    }

    public function donate()
    {
        $this->set([
            'pageTitle' => 'Donate to the Muncie Arts and Culture Council'
        ]);
    }

    public function completeDonation()
    {
        // No validation or recording currently takes place for donations
        $this->viewBuilder()->layout('json');
        $this->set([
            '_serialize' => true,
            'retval' => [
                'success' => true
            ]
        ]);
    }

    public function donationComplete()
    {
        $this->set('pageTitle', 'Thank you!');
    }

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
