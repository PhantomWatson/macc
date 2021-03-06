<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Event\EmailListener;
use App\Model\Entity\Membership;
use App\Model\Entity\Payment;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\I18n\Time;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;

/**
 * Class PaymentsController
 * @package App\Controller\Admin
 * @property \App\Model\Table\PaymentsTable $Payments
 * @property \App\Model\Table\MembershipsTable $Memberships
 * @property \App\Model\Table\UsersTable $Users
 * @method Payment[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class PaymentsController extends AppController
{
    use MailerAwareTrait;

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

    /**
     * Initialize method
     *
     * @return void
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();
        $emailListener = new EmailListener();
        EventManager::instance()->on($emailListener);
    }

    public function index()
    {
        $payments = $this->paginate($this->Payments)->toArray();
        $this->set([
            'pageTitle' => 'Payment Records',
            'payments' => $payments
        ]);
    }

    /**
     * Page for manually adding a payment record
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $membershipLevelsTable = TableRegistry::getTableLocator()->get('MembershipLevels');
        $results = $membershipLevelsTable->find('all')
            ->select(['id', 'name', 'cost'])
            ->order(['cost' => 'ASC']);
        $membershipLevels = [];
        $costs = [];
        foreach ($results as $membershipLevel) {
            $membershipLevels[$membershipLevel->id] = sprintf(
                '%s ($%s)',
                $membershipLevel->name,
                number_format($membershipLevel->cost)
            );
            $costs[$membershipLevel->id] = $membershipLevel->cost;
        }

        $payment = $this->Payments->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['admin_adder_id'] = $this->Auth->user('id');
            $data['postback'] = '';
            $membershipLevelId = $this->request->getData('membership_level_id');
            $data['amount'] = isset($costs[$membershipLevelId]) ? $costs[$membershipLevelId] : 0;
            /** @var Payment $payment */
            $payment = $this->Payments->patchEntity($payment, $data);
            $errors = $payment->getErrors();
            if (empty($errors)) {
                $payment = $this->Payments->save($payment);
                $this->Flash->success('Payment record added');

                // Add membership
                $userId = $this->request->getData('user_id');
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
                    $errors = $membership->getErrors();
                    if (empty($errors)) {
                        $this->Memberships->save($membership);
                        $this->Flash->success('One year of membership added to that user\'s account');
                        $this->getMailer('Membership')->send('membershipAddedByAdmin', [$membership]);

                        // Dispatch event
                        $eventName = 'Model.Membership.afterAdminGranted';
                        $adminUserName = $this->Auth->user('name');
                        $metadata = ['meta' => compact('adminUserName', 'membership')];
                        $event = new Event($eventName, $this, $metadata);
                        EventManager::instance()->dispatch($event);
                    } else {
                        $this->Flash->error(
                            'There was an error adding one year of membership to that user\'s account.'
                        );
                    }
                }

                return $this->redirect([
                    'action' => 'index'
                ]);
            }
            $this->Flash->error('There was an error adding a new payment record');
        }

        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $users = $usersTable->find('list')->order(['name' => 'ASC']);

        // Automatically select user
        $queryStringUserId = $this->request->getQuery('u');
        if (!$this->request->getData('user_id') && is_numeric($queryStringUserId)) {
            $payment->user_id = $queryStringUserId;
        }

        $this->set([
            'membershipLevels' => $membershipLevels,
            'pageTitle' => 'Add a New Membership Payment',
            'payment' => $payment,
            'users' => $users
        ]);

        return null;
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
