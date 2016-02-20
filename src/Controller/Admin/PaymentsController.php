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
}
