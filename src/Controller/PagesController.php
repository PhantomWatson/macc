<?php
namespace App\Controller;

class PagesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow();
    }

    public function home()
    {
        $this->set('pageTitle', '');
    }

    public function styling()
    {
        $this->set('pageTitle', 'Profile Styling Guide');
    }
}
