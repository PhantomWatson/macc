<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\Entity\Membership;
use App\Model\Entity\Payment;
use App\Model\Table\MembershipsTable;
use App\Model\Table\PaymentsTable;
use App\Model\Table\UsersTable;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

/**
 * Class PaymentsController
 * @package App\Controller\Admin
 * @property PaymentsTable $Payments
 * @property MembershipsTable $Memberships
 * @property UsersTable $Users
 * @method Payment[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class PaymentsController extends AppController
{
    public $paginate = [
        'contain' => [
            'MembershipLevels' => ['fields' => ['id', 'name']],
            'Refunders' => ['fields' => ['id', 'name']],
            'AdminAdders' => ['fields' => ['id', 'name']],
            'Users' => ['fields' => ['id', 'name']]
        ],
        'fields' => [
            'Payments.admin_adder_id',
            'Payments.created',
            'Payments.id',
            'Payments.notes',
            'Payments.refunded_date',
            'Payments.refunder_id',
            'Users.id',
            'Users.name',
        ],
        'limit' => 10,
        'order' => ['Payments.created' => 'DESC']
    ];

    public function index()
    {
        $payments = $this->paginate($this->Payments)->toArray();
        $this->set([
            'pageTitle' => 'Payment Records',
            'payments' => $payments
        ]);
    }

    public function add()
    {
        $membershipLevelsTable = TableRegistry::get('MembershipLevels');
        $results = $membershipLevelsTable->find('all')
            ->select(['id', 'name', 'cost'])
            ->order(['cost' => 'ASC']);
        $membershipLevels = [];
        $costs = [];
        foreach ($results as $membershipLevel) {
            $membershipLevels[$membershipLevel->id] = $membershipLevel->name.' ($'.number_format($membershipLevel->cost).')';
            $costs[$membershipLevel->id] = $membershipLevel->cost;
        }

        $payment = $this->Payments->newEntity();
        if ($this->request->is('post')) {
            $this->request->data['admin_adder_id'] = $this->Auth->user('id');
            $this->request->data['postback'] = '';
            $membershipLevelId = $this->request->data('membership_level_id');
            $amount = isset($costs[$membershipLevel->id]) ? $costs[$membershipLevel->id] : 0;
            $this->request->data['amount'] = $amount;
            /** @var Payment $payment */
            $payment = $this->Payments->patchEntity($payment, $this->request->data());
            $errors = $payment->errors();
            if (empty($errors)) {
                $payment = $this->Payments->save($payment);
                $this->Flash->success('Payment record added');

                // Add membership
                $userId = $this->request->data('user_id');
                if ($membershipLevelId && $userId) {
                    $this->loadModel('Memberships');
                    /** @var Membership $membership */
                    $membership = $this->Memberships->newEntity([
                        'expires' => new Time(strtotime('+1 year')),
                        'membership_level_id' => $membershipLevelId,
                        'payment_id' => $payment->id,
                        'auto_renew' => 0,
                        'user_id' => $userId
                    ]);
                    $errors = $membership->errors();
                    if (empty($errors)) {
                        $membership = $this->Memberships->save($membership);
                        $this->Flash->success('One year of membership added to that user\'s account');
                    } else {
                        $this->Flash->error('There was an error adding one year of membership to that user\'s account.');
                    }
                }

                return $this->redirect([
                    'action' => 'index'
                ]);
            }
            $this->Flash->error('There was an error adding a new payment record');
        }

        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->find('list')->order(['name' => 'ASC']);

        $this->set([
            'membershipLevels' => $membershipLevels,
            'pageTitle' => 'Add a New Payment Record',
            'payment' => $payment,
            'users' => $users
        ]);
    }

    public function refund($paymentId)
    {
        $this->request->allowMethod(['post']);

        try {
            $payment = $this->Payments->get($paymentId);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error("Payment record #$paymentId not found.");
            return $this->redirect(['action' => 'index']);
        }

        // Bounce user back if the payment was already refunded
        if ($payment->refunded_date) {
            $timestamp = strtotime($payment->refunded_date);
            $date = date('F j, Y', $timestamp);
            $this->loadModel('Users');
            try {
                $user = $this->Users->get($payment->refunder_id);
                $admin = $user->name;
            } catch (RecordNotFoundException $e) {
                $admin = "(unknown user #$payment->refunder_id)";
            }
            $this->Flash->error("That payment record was already marked refunded on $date by $admin.");
        } else {
            // Record refund
            $payment->refunded_date = date('Y-m-d H:i:s');
            $payment->refunder_id = $this->Auth->user('id');
            if ($this->Payments->save($payment)) {
                $this->Flash->success('Refund recorded.');
            } else {
                $this->Flash->error('There was an error saving that refund record.');
            }
        }

        return $this->redirect(['action' => 'index']);
    }
}
