<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\Network\Exception\InternalErrorException;

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

        // Create Stripe customer
        $apiKey = Configure::read('Stripe.Secret');
        \Stripe\Stripe::setApiKey($apiKey);
        $token = $this->request->data('stripeToken');
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
                'description' => "$user->name purchasing '$membershipLevel->name' membership"
            ]);
        } catch (\Stripe\Error\Card $e) {
            $this->set('retval', [
                'success' => false,
                'message' => 'Credit card was declined.'
            ]);
            $this->response->statusCode('402');
            return $this->render();
        }

        // Save payment record in MACC's database
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
     * Checks for and processes any recurring payments associated with
     * memberships that will expire in the next 24 hours.
     *
     * Intended for a cron job, but can be run manually.
     */
    public function processRecurring()
    {
        $apiKey = Configure::read('Stripe.Secret');
        \Stripe\Stripe::setApiKey($apiKey);

        $this->loadModel('Memberships');
        $memberships = $this->Memberships->find('toAutoRenew');

        if ($memberships->isEmpty()) {
            $this->Flash->set('No memberships need to be renewed at this time.');
        }

        foreach ($memberships as $membership) {
            $this->validateMembership($membership);
            $amount = $membership->membership_level['cost'].'00'; // Cost is stored as dollars
            $userName = $membership->user['name'];
            $membershipLevelName = $membership->membership_level['name'];

            $charge = $this->createStripeCharge([
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
            ]);

            if (! $charge->paid) {
                throw new InternalErrorException('Charge did not complete successfully');
            }

            // Save payment
            $payment = $this->Payments->newEntity([
                'user_id' => $membership->user_id,
                'membership_level_id' => $membership->membership_level_id,
                'amount' => $membership->membership_level['cost']
            ]);
            $errors = $payment->errors();
            if (! empty($errors)) {
                throw new InternalErrorException('Errors saving payment record: '.json_encode($errors));
            }
            $payment = $this->Payments->save($payment);

            // Mark previous membership as having been renewed
            $membership = $this->Memberships->patchEntity($membership, [
                'renewed' => new Time()
            ]);
            $errors = $membership->errors();
            if (! empty($errors)) {
                throw new InternalErrorException('Errors updating membership record: '.json_encode($errors));
            }
            $membership = $this->Memberships->save($membership);

            // Save new membership
            $newMembership = $this->Memberships->newEntity([
                'user_id' => $membership->user_id,
                'membership_level_id' => $membership->membership_level_id,
                'payment_id' => $payment->id,
                'recurring_billing' => true,
                'expires' => new Time(strtotime('+1 year'))
            ]);
            $errors = $newMembership->errors();
            if (! empty($errors)) {
                throw new InternalErrorException('Errors saving new membership record: '.json_encode($errors));
            }
            $newMembership = $this->Memberships->save($newMembership);

            $this->Flash->success('Membership renewed for '.$membership->user['name']);
        }

        $this->set([
            'pageTitle' => 'Process Recurring Payments'
        ]);
    }

    /**
     * Creates a Stripe charge object (charges the user) and handles various exceptions.
     *
     * @param array $params Passed to \Stripe\Charge::create()
     * @return \Stripe\Charge
     */
    private function createStripeCharge($params)
    {
        try {
            $charge = \Stripe\Charge::create($params);
        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $this->throwStripeException($e);
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

        return $charge;
    }

    /**
     * Turns a Stripe exception into a CakePHP exception.
     *
     * This doesn't currently provide any advantage, but it helps
     * simplify createStripeCharge(), which might be modified in
     * the future to handle different Stripe exceptions differently.
     *
     * @param Exception $3
     * @throws InternalErrorException
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
}
