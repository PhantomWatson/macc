<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

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
        $payment = $this->Payments->newEntity();
        if ($this->request->is('post')) {
            $this->request->data['admin_adder_id'] = $this->Auth->user('id');
            $this->request->data['postback'] = '';
            $payment = $this->Payments->patchEntity($payment, $this->request->data());
            $errors = $payment->errors();
            if (empty($errors) && $this->Payments->save($payment)) {
                $this->Flash->success('Payment record added');
                return $this->redirect([
                    'action' => 'index'
                ]);
            }
            $this->Flash->error('There was an error adding a new payment record');
        }

        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->find('list')->order(['name' => 'ASC']);

        $membershipLevelsTable = TableRegistry::get('MembershipLevels');
        $results = $membershipLevelsTable->find('all')
            ->select(['id', 'name', 'cost'])
            ->order(['cost' => 'ASC']);
        $membershipLevels = [];
        foreach ($results as $membershipLevel) {
            $membershipLevels[$membershipLevel->id] = $membershipLevel->name.' ($'.number_format($membershipLevel->cost).')';
        }

        $this->set([
            'membershipLevels' => $membershipLevels,
            'pageTitle' => 'Add a New Payment Record',
            'payment' => $payment,
            'users' => $users
        ]);
    }
}
