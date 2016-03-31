<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;

class DonationsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow([
            'completeDonation',
            'donate',
            'donationComplete'
        ]);
        if (Configure::read('forceSSL')) {
            $this->loadComponent('Security', ['blackHoleCallback' => 'forceSSL']);
            $this->Security->requireSecure(['donate']);
        }
    }

    public function beforeFilter(\Cake\Event\Event $event)
    {
         /* Prevent Security component from stripping out "unknown fields"
          * from AJAX request to completeDonation and causing errors
          * http://book.cakephp.org/3.0/en/controllers/components/security.html#form-tampering-prevention */
         $this->Security->config('unlockedActions', ['completeDonation']);
    }

    public function donate()
    {
        $this->set([
            'pageTitle' => 'Donate to the Muncie Arts and Culture Council'
        ]);
    }

    public function completeDonation()
    {
        // Validate amount
        $amount = $this->request->data('amount');
        if (! is_numeric($amount)) {
            throw new ForbiddenException('Donation amount must be numeric');
        } elseif ($amount < 1) {
            throw new ForbiddenException('Donation must be at least one dollar');
        }

        $metadata = [];
        if ($this->Auth->user('id')) {
            $metadata['Donor name'] = $this->Auth->user('name');
        } else {
            $metadata['Donor name'] = '';
        }
        $metadata['Donor email'] = $this->request->data('email');

        // Create the charge on Stripe's servers - this will charge the user's card
        $apiKey = Configure::read('Stripe.Secret');
        \Stripe\Stripe::setApiKey($apiKey);
        try {
            $description = 'Donation to MACC of $'.number_format($amount, 2);
            $charge = \Stripe\Charge::create([
                'amount' => $amount * 100, // amount in cents
                'currency' => 'usd',
                'source' => $this->request->data('stripeToken'),
                'description' => $description,
                'metadata' => $metadata,
                'receipt_email' => $this->request->data('email')
            ]);
        } catch (\Stripe\Error\Card $e) {
            throw new ForbiddenException('The provided credit card has been declined');
        }

        $this->viewBuilder()->layout('json');
        $this->set([
            '_serialize' => ['retval'],
            'retval' => ['success' => true]
        ]);
    }

    public function donationComplete()
    {
        $this->set('pageTitle', 'Thank you!');
    }
}
