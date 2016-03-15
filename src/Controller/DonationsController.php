<?php
namespace App\Controller;

use App\Controller\AppController;

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
        $this->Security->requireSecure(['donate']);
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
