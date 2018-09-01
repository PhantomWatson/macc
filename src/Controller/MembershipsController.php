<?php
namespace App\Controller;

use App\Event\EmailListener;
use App\Model\Entity\Membership;
use App\Model\Entity\Payment;
use App\Model\Entity\User;
use App\Model\Table\MembershipRenewalLogsTable;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;

/**
 * Memberships Controller
 *
 * @property \App\Model\Table\MembershipsTable $Memberships
 * @property \App\Model\Table\PaymentsTable $Payments
 * @property \App\Model\Table\MembershipLevelsTable $MembershipLevels
 * @property \App\Model\Table\UsersTable $Users
 */
class MembershipsController extends AppController
{
    use MailerAwareTrait;

    /**
     * Initialize method
     *
     * @return void
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow([
            'enterPayment',
            'level',
            'levels',
            'processRecurring'
        ]);
        $emailListener = new EmailListener();
        EventManager::instance()->on($emailListener);
    }

    /**
     * BeforeFilter method
     *
     * @param Event $event A CakePHP event object
     * @return Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        /* Prevent Security component from stripping out "unknown fields"
         * from AJAX request to completePurchase and causing errors
         * http://book.cakephp.org/3.0/en/controllers/components/security.html#form-tampering-prevention */
        if (Configure::read('forceSSL')) {
            $this->Security->getConfig('unlockedActions', ['completePurchase']);
        }
    }

    /**
     * Page displayed to users after purchasing a membership
     *
     * @return void
     */
    public function purchaseComplete()
    {
        $userId = $this->Auth->user('id');
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');
        $membershipCount = $membershipsTable->find()
            ->where(['user_id' => $userId])
            ->count();

        $this->set([
            'pageTitle' => 'Membership Purchased!',
            'isFirstMembershipPurchase' => $membershipCount == 1
        ]);
    }

    /**
     * Page displaying information about the user's current membership
     *
     * @return void
     */
    public function myMembership()
    {
        $userId = $this->Auth->user('id');
        $membership = $this->Memberships->getCurrentMembership($userId);
        if ($membership && $membership->payment_id) {
            $this->loadModel('Payments');
            $payment = $this->Payments->get($membership->payment_id);
            $canBeAutoRenewed = $payment->stripe_charge_id != null;
        } else {
            $canBeAutoRenewed = false;
        }

        $this->set([
            'canBeAutoRenewed' => $canBeAutoRenewed,
            'membership' => $membership,
            'pageTitle' => 'My Membership Info'
        ]);
    }

    /**
     * Page for toggling automatic membership renewal on or off
     *
     * @param null|int $value Either 1 or 0 for toggling on or off
     * @return void
     */
    public function toggleAutoRenewal($value = null)
    {
        if (! $this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        if (! in_array($value, ['1', '0'])) {
            throw new BadRequestException('Invalid value supplied');
        }

        $userId = $this->Auth->user('id');
        $membership = $this->Memberships->getCurrentMembership($userId);

        if ($membership && $membership->payment_id) {
            $this->loadModel('Payments');
            $payment = $this->Payments->get($membership->payment_id);
            $canBeAutoRenewed = $payment->stripe_charge_id != null;
        } else {
            $canBeAutoRenewed = false;
        }

        if ($value == 1 && ! $canBeAutoRenewed) {
            throw new BadRequestException(
                'Cannot turn on automatic renewal, since initial payment was not made online'
            );
        }

        $membership = $this->Memberships->patchEntity($membership, [
            'auto_renew' => $value
        ]);
        $this->Memberships->save($membership);
        $msg = 'Membership auto-renewal turned ' . ($value ? 'on' : 'off') . '.';
        if ($value) {
            $timestamp = $membership->expires->format('U') - (60 * 60 * 24);
            $msg .= ' Your membership will be automatically renewed on ' . date('F j, Y', $timestamp) . '.';
        }
        $this->Flash->success($msg);
        $this->redirect($this->referer());
    }

    /**
     * Page used as the postUrl for purchases, charges the customer or returns an error status code on error
     *
     * @return Response
     */
    public function completePurchase()
    {
        $this->viewBuilder()->setLayout('json');
        $this->set('_serialize', ['retval']);

        // Verify user
        $userId = $this->request->getData('userId');
        $this->loadModel('Users');
        try {
            /** @var User $user */
            $user = $this->Users->get($userId);
        } catch (\Cake\Datasource\Exception\InvalidPrimaryKeyException $e) {
            $this->set('retval', [
                'success' => false,
                'message' => "Error: No valid user ID"
            ]);
            $this->response = $this->response->withStatus('404');

            return $this->response;
        } catch (RecordNotFoundException $e) {
            $this->set('retval', [
                'success' => false,
                'message' => "Sorry, but that user account ('$userId') was not found."
            ]);
            $this->response = $this->response->withStatus('404');

            return $this->response;
        }

        // Verify membership level
        $membershipLevelId = $this->request->getData('membershipLevelId');
        $this->loadModel('MembershipLevels');
        try {
            $membershipLevel = $this->MembershipLevels->get($membershipLevelId);
        } catch (RecordNotFoundException $e) {
            $this->set('retval', [
                'success' => false,
                'message' => "Sorry, but that membership level ('$membershipLevelId') was not found."
            ]);
            $this->response = $this->response->withStatus('404');

            return $this->response;
        }

        // Create Stripe customer
        $apiKey = Configure::read('Stripe.Secret');
        \Stripe\Stripe::setApiKey($apiKey);
        $token = $this->request->getData('stripeToken');
        $customer = $this->Users->createStripeCustomer($userId, $token);
        $user = $this->Users->patchEntity($user, [
            'stripe_customer_id' => $customer->id
        ]);
        $user = $this->Users->save($user);

        // Charge customer
        try {
            $charge = \Stripe\Charge::create([
                'amount' => $membershipLevel->cost.'00', // in cents
                'currency' => 'usd',
                'customer' => $customer->id,
                'description' => "$user->name purchasing '$membershipLevel->name' membership",
                'metadata' => [
                    'macc_user_id' => $userId,
                    'membership_level_id' => $membershipLevelId
                ]
            ]);
        } catch (\Stripe\Error\Card $e) {
            $this->set('retval', [
                'success' => false,
                'message' => 'Credit card was declined.'
            ]);
            $this->response = $this->response->withStatus('402');

            return $this->response;
        }

        // Save payment record in MACC's database
        $this->loadModel('Payments');
        $paymentData = [
            'user_id' => $userId,
            'membership_level_id' => $membershipLevelId,
            'amount' => $membershipLevel->cost,
            'stripe_charge_id' => $charge->id
        ];
        if (Configure::read('Stripe.mode') == 'Test') {
            $paymentData['notes'] = 'Payment made in Stripe test mode';
        }
        $payment = $this->Payments->newEntity($paymentData);
        $errors = $payment->getErrors();
        if (empty($errors)) {
            $payment = $this->Payments->save($payment);

            // Save membership
            $this->loadModel('Memberships');
            $membership = $this->Memberships->newEntity([
                'user_id' => $userId,
                'membership_level_id' => $membershipLevelId,
                'payment_id' => $payment->id,
                'auto_renew' => $this->request->getData('autoRenew'),
                'expires' => new Time(strtotime('+1 year'))
            ]);
            $errors = $membership->getErrors();
            if (empty($errors)) {
                $hadPreviousMembership = $this->Users->hasMembership($userId);
                $membership = $this->Memberships->save($membership);

                // Turn off any previous membership's auto_renew flag
                $this->Memberships->disablePreviousAutoRenewal($userId, $membership->id);

                // Dispatch event
                if (!$hadPreviousMembership) {
                    $eventName = 'Model.Membership.afterFirstPurchase';
                    $metadata = ['meta' => compact('membership')];
                    $event = new Event($eventName, $this, $metadata);
                    EventManager::instance()->dispatch($event);
                }

                $this->set('retval', [
                    'success' => true,
                    'message' => 'Purchase completed!'
                ]);
                return $this->render();
            }
        }

        $msg = 'There was an error processing your payment. ' . $this->getContactAdminMessage();
        $this->set('retval', [
            'success' => false,
            'message' => $msg
        ]);
        $this->response = $this->response->withStatus('500');

        return $this->response;
    }

    /**
     * Checks for and processes any recurring payments associated with
     * memberships that will expire in the next 24 hours.
     *
     * Intended for a cron job, but can be run manually.
     *
     * @return void
     */
    public function processRecurring()
    {
        /** @var MembershipRenewalLogsTable $logsTable */
        $logsTable = TableRegistry::getTableLocator()->get('MembershipRenewalLogs');
        $apiKey = Configure::read('Stripe.Secret');
        \Stripe\Stripe::setApiKey($apiKey);

        $this->loadModel('Memberships');
        $memberships = $this->Memberships->find('toAutoRenew');

        $results = [];

        if ($memberships->isEmpty()) {
            $errorMsg = 'No memberships need to be renewed at this time.';
            $logsTable->logAutoRenewal($errorMsg);
            $results[] = $errorMsg;
        }

        $chargedUsers = [];
        foreach ($memberships as $membership) {
            /** @var Membership $membership */
            if (in_array($membership->user_id, $chargedUsers)) {
                continue;
            }

            $this->validateMembership($membership);
            $chargeParams = $this->getAutoRenewalChargeParams($membership);
            $errorMsg = null;

            try {
                $charge = $this->createStripeCharge($chargeParams);

            // User's card was declined
            } catch (\Stripe\Error\Card $e) {
                // Email user
                $this->getMailer('Membership')
                    ->send('autoRenewFailedCardDeclined', [$membership]);

                // Turn off auto-renewal
                $membership = $this->Memberships->patchEntity($membership, ['auto_renew' => 0]);
                $this->Memberships->save($membership);

                $errorMsg = $this->getCardDeclinedErrorMsg($membership);
            } catch (\Exception $e) {
                $errorMsg = $this->getChargeErrorMsg($membership, $e);
                $this->getMailer('Membership')
                    ->send('errorRenewingMembership', [$membership, $errorMsg]);
            }

            if (!isset($charge) || !$charge->paid) {
                $errorMsg = $errorMsg ?? $this->getChargeErrorMsg($membership);
                $this->getMailer('Membership')
                    ->send('errorRenewingMembership', [$membership, $errorMsg]);
                $logsTable->logAutoRenewal($errorMsg, true);
                Log::write('error', $errorMsg);
                $results[] = $errorMsg;
                continue;
            }

            // Save payment
            $payment = $this->createAutoRenewPayment($membership, $charge->id);
            $errors = $payment->getErrors();
            if (!empty($errors)) {
                $errorMsg = $this->getPaymentRecordErrorMsg($membership, $errors, $charge->id);
                $this->getMailer('Membership')
                    ->send('errorRenewingMembership', [$membership, $errorMsg]);
                Log::write('error', $errorMsg);
                $logsTable->logAutoRenewal($errorMsg, true);
                $results[] = $errorMsg;
                continue;
            }
            $this->Payments->save($payment);

            // Turn off previous membership's auto_renew flag
            $membership = $this->Memberships->patchEntity($membership, ['auto_renew' => 0]);
            $errors = $membership->getErrors();
            if (!empty($errors)) {
                $errorMsg = $this->getMembershipSavingErrorMsg($membership);
                $this->getMailer('Membership')
                    ->send('errorRenewingMembership', [$membership, $errorMsg]);
                $logsTable->logAutoRenewal($errorMsg, true);
                $results[] = $errorMsg;
                continue;
            }
            $this->Memberships->save($membership);

            // Save new membership
            $newMembership = $this->createAutoRenewMembership($membership, $payment);
            $errors = $newMembership->getErrors();
            if (!empty($errors)) {
                $errorMsg = $this->getMembershipSavingErrorMsg($membership);
                $this->getMailer('Membership')
                    ->send('errorRenewingMembership', [$membership, $errorMsg]);
                $logsTable->logAutoRenewal($errorMsg, true);
                $results[] = $errorMsg;
                continue;
            }
            $this->Memberships->save($newMembership);

            // Turn off any previous membership's auto_renew flag
            $this->Memberships->disablePreviousAutoRenewal($membership->user_id, $newMembership->id);

            // Prevent this user from being charged again in this loop
            $chargedUsers[] = $membership->user_id;

            // Email user
            $this->getMailer('Membership')->send('membershipAutoRenewed', [$membership]);

            $msg = 'Membership renewed for ' . $membership->user['name'];
            $logsTable->logAutoRenewal($msg);
            $results[] = $msg;
        }

        $this->set([
            'results' => $results,
            'pageTitle' => 'Process Recurring Payments'
        ]);
    }

    /**
     * Returns an error message indicating that a charge failed
     *
     * @param Membership $membership Membership entity
     * @param null|\Exception $exception Optional exception
     * @return string
     */
    private function getChargeErrorMsg($membership, $exception = null)
    {
        return sprintf(
            'Charge did not complete successfully when attempting to automatically renew ' .
            '%s\'s %s membership.%s (user ID: %s; email: %s)',
            $membership->user['name'],
            $membership->membership_level['name'],
            $exception ? ': ' . $exception->getMessage() : '',
            $membership->user_id,
            $membership->user['email']
        );
    }

    /**
     * Creates a Stripe charge object (charges the user) and handles various exceptions other than 'card declined'
     *
     * @param array $params Passed to \Stripe\Charge::create()
     * @return \Stripe\Charge
     * @throws InternalErrorException
     */
    private function createStripeCharge($params)
    {
        try {
            $charge = \Stripe\Charge::create($params);
        } catch (\Stripe\Error\RateLimit $e) {
            // Too many requests made to the API too quickly
            $this->throwStripeException($e);
        } catch (\Stripe\Error\InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API
            $this->throwStripeException($e);
        } catch (\Stripe\Error\Authentication $e) {
            // Authentication with Stripe's API failed
            $this->throwStripeException($e);
        } catch (\Stripe\Error\ApiConnection $e) {
            // Network communication with Stripe failed
            $this->throwStripeException($e);
        } catch (\Stripe\Error\Base $e) {
            // Display a very generic error to the user
            $this->throwStripeException($e);
        }

        if (!isset($charge)) {
            throw new InternalErrorException('Cannot create Stripe charge: unknown error');
        }

        return $charge;
    }

    /**
     * Turns a Stripe exception into a CakePHP exception.
     *
     * This doesn't currently provide any advantage, but it helps
     * simplify createStripeCharge(), which might be modified in
     * the future to handle different Stripe exceptions differently.
     *
     * @param \Stripe\Error\Base $e
     * @throws InternalErrorException
     * @return void
     */
    private function throwStripeException($e)
    {
        $body = $e->getJsonBody();
        $err  = $body['error'];
        throw new InternalErrorException($err['message']);
    }

    /**
     * Checks membership and throws exceptions if any of its associations are broken.
     *
     * @param Membership $membership
     * @throws NotFoundException
     * @return void
     */
    private function validateMembership($membership)
    {
        if (empty($membership->membership_level)) {
            throw new NotFoundException('Membership level #'.$membership->membership_level_id.' not found.');
        }
        if (empty($membership->membership_level['cost'])) {
            throw new NotFoundException('Membership level #'.$membership->membership_level_id.' has no cost.');
        }
        if (empty($membership->user)) {
            throw new NotFoundException('User #'.$membership->user_id.' not found.');
        }
        if (empty($membership->user['stripe_customer_id'])) {
            throw new NotFoundException('User #'.$membership->user_id.' has no Stripe customer id.');
        }
    }

    /**
     * Page that displays all current membership levels
     *
     * @return void
     */
    public function levels()
    {
        // Notify user of their current membership status
        $this->loadModel('Memberships');
        $userId = $this->Auth->user('id');
        $expirationWarning = $this->Memberships->getMembershipExpirationWarning($userId);
        if ($expirationWarning) {
            $this->Flash->set($expirationWarning);
        }

        $this->loadModel('MembershipLevels');
        $membershipLevels = $this->MembershipLevels
            ->find('all')
            ->order(['cost' => 'ASC']);
        $this->set([
            'membershipLevels' => $membershipLevels,
            'pageTitle' => 'Become a Member'
        ]);
    }

    /**
     * Page that displays information about a specific membership level
     *
     * @param null $membershipLevelId
     * @return Response|null
     * @throws \Exception
     */
    public function level($membershipLevelId)
    {
        // No membership level specified
        if (!$membershipLevelId) {
            return $this->redirect([
                'controller' => 'Memberships',
                'action' => 'levels'
            ]);
        }

        $renewing = (bool)$this->request->getQuery('renewing');
        if ($renewing && !$this->Auth->user()) {
            $this->Flash->set('Please log in before continuing');
            return $this->redirectToLogin();
        }

        $this->loadModel('Users');
        $user = $this->Users->newEntity();

        if ($this->request->is('post')) {
            $autoRenew = $this->request->getData('renewal') == 'automatic';
            $redirectToPayment = function () use ($membershipLevelId, $autoRenew) {
                return $this->redirect([
                    'controller' => 'Memberships',
                    'action' => 'enterPayment',
                    '?' => [
                        'memberLevelId' => $membershipLevelId,
                        'autoRenew' => $autoRenew ? 1 : 0
                    ]
                ]);
            };

            // User is already logged in
            if ($this->Auth->user()) {
                return $redirectToPayment();
            }

            // Redirect to login if email address found
            $email = $this->request->getData('email');
            $email = trim($email);
            $email = strtolower($email);
            if ($this->Users->exists(['email' => $email])) {
                $this->Flash->set(
                    'Your email address has already been used to register an account. Please log in before proceeding.'
                );
                return $this->redirectToLogin();
            }

            // Register user and log them in
            /** @var User|bool $result */
            $result = $this->processRegister();
            if ($result) {
                $password = $this->request->getData('new_password');
                $this->request = $this->request->withData('password', $password);

                // Redirect to payment page
                if ($this->Auth->identify()) {
                    $this->Auth->setUser($result->toArray());
                    $this->Flash->success(
                        'Your new website account has been created and you have been logged in.'
                    );

                    return $redirectToPayment();

                // Redirect to login
                } else {
                    $this->Flash->error(
                        'There was an error automatically logging you in. Please manually log in to proceed.'
                    );

                    return $this->redirectToLogin();
                }
            }
        } else {
            $user->mailing_list = true;
        }

        // Clear password fields
        $this->request = $this->request->withData('new_password', null);
        $this->request = $this->request->withData('confirm_password', null);

        $this->loadModel('MembershipLevels');
        $membershipLevel = $this->MembershipLevels->get($membershipLevelId);
        $pageTitle = ($renewing ? 'Renew Membership' : 'Become a Member of MACC') . " ($membershipLevel->name level)";
        $this->set([
            'membershipLevel' => $membershipLevel,
            'pageTitle' => $pageTitle,
            'renewing' => $renewing,
            'user' => $user
        ]);

        return null;
    }

    /**
     * Page for displaying the Stripe payment prompt
     *
     * @return Response|null
     */
    public function enterPayment()
    {
        $memberLevelId = $this->request->getQuery('memberLevelId');
        $autoRenew = (bool)$this->request->getQuery('autoRenew');
        $this->loadModel('MembershipLevels');
        if (!$memberLevelId || !$this->MembershipLevels->exists(['id' => $memberLevelId])) {
            $this->Flash->error(
                'Sorry, there was an error loading the payment method form. ' . $this->getContactAdminMessage()
            );

            return $this->redirect('/');
        }

        if (!$this->Auth->user()) {
            $this->Flash->error('Please log in before proceeding with purchase.');

            return $this->redirectToLogin();
        }

        $this->set([
            'autoRenew' => $autoRenew,
            'membershipLevel' => $this->MembershipLevels->get($memberLevelId),
            'pageTitle' => "Payment Information"
        ]);

        return null;
    }

    /**
     * Returns an array of parameters for a Stripe charge for renewing a membership
     *
     * @param Membership $membership
     * @return array
     */
    private function getAutoRenewalChargeParams(Membership $membership)
    {
        $amount = $membership->membership_level['cost'].'00'; // Cost is stored as dollars
        $userName = $membership->user['name'];
        $membershipLevelName = $membership->membership_level['name'];

        return [
            'amount'   => $amount,
            'currency' => 'usd',
            'customer' => $membership->user['stripe_customer_id'],
            'description' => "Automatically renewing $userName's \"$membershipLevelName\" membership",
            'metadata' => [
                'macc_user_id' => $membership->user_id,
                'membership_level_id' => $membership->membership_level_id
            ],
            'receipt_email' => $membership->user['email'],
            'statement_descriptor' => 'MACC member renewal' // 22 characters max
        ];
    }

    /**
     * Creates a new payment entity for the current auto-renewal process
     *
     * @param Membership $membership Membership entity
     * @param string $chargeId ID for a successful Stripe charge
     * @return Payment
     */
    private function createAutoRenewPayment(Membership $membership, string $chargeId)
    {
        $paymentParams = [
            'user_id' => $membership->user_id,
            'membership_level_id' => $membership->membership_level_id,
            'amount' => $membership->membership_level['cost'],
            'stripe_charge_id' => $chargeId
        ];
        $this->loadModel('Payments');

        return $this->Payments->newEntity($paymentParams);
    }

    /**
     * Returns a 'card declined' error message
     *
     * @param Membership $membership Membership entity
     * @return string
     */
    private function getCardDeclinedErrorMsg(Membership $membership)
    {
        return sprintf(
            'Card was declined when attempting to automatically renew %s\'s %s membership. ' .
            '(user ID: %s; email: %s)',
            $membership->user['name'],
            $membership->membership_level['name'],
            $membership->user_id,
            $membership->user['email']
        );
    }

    /**
     * Returns a 'payment record could not be saved' error message
     *
     * @param Membership $membership Membership entity
     * @param array $errors Payment record errors
     * @param string $chargeId Stripe charge ID
     * @return string
     */
    private function getPaymentRecordErrorMsg(Membership $membership, array $errors, string $chargeId)
    {
        return sprintf(
            'Error saving payment record when attempting to automatically renew %s\'s %s membership: ' .
            '%s (user ID: %s; email: %s; Stripe charge ID: %s)',
            $membership->user['name'],
            $membership->membership_level['name'],
            json_encode($errors),
            $membership->user_id,
            $membership->user['email'],
            $chargeId
        );
    }

    /**
     * Returns a 'could not save/update membership record' error message
     *
     * @param Membership $membership Membership entity
     * @return string
     */
    private function getMembershipSavingErrorMsg(Membership $membership)
    {
        return sprintf(
            'Error saving membership record when attempting to automatically renew %s\'s %s membership: ' .
            '%s (user ID: %s; email: %s; Stripe charge ID: %s)',
            $membership->user['name'],
            $membership->membership_level['name'],
            json_encode($membership->getErrors()),
            $membership->user_id,
            $membership->user['email']
        );
    }

    /**
     * Creates a new membership entity for the one currently being auto-renewed
     *
     * @param Membership $existingMembership Membership entity
     * @param Payment $payment Payment entity
     * @return Membership
     */
    private function createAutoRenewMembership(Membership $existingMembership, Payment $payment)
    {
        $membershipParams = [
            'user_id' => $existingMembership->user_id,
            'membership_level_id' => $existingMembership->membership_level_id,
            'payment_id' => $payment->id,
            'auto_renew' => 1,
            'expires' => new Time(strtotime('+1 year'))
        ];

        return $this->Memberships->newEntity($membershipParams);
    }
}
