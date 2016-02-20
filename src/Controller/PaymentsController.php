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
}
