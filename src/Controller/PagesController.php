<?php
namespace App\Controller;

class PagesController extends AppController
{
    /**
     * Initialize method
     *
     * @return void
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow();
    }

    /**
     * Home page
     *
     * @return void
     */
    public function home()
    {
        $this->set('pageTitle', '');
    }

    /**
     * Page that explains markdown used in profile pages
     *
     * @return void
     */
    public function styling()
    {
        $this->set('pageTitle', 'Profile Styling Guide');
    }

    /**
     * Privacy policy page
     *
     * @return void
     */
    public function privacy()
    {
        $this->set('pageTitle', 'Privacy Policy');
    }

    /**
     * Terms of service page
     *
     * @return void
     */
    public function terms()
    {
        $this->set('pageTitle', 'Terms of Service');
    }
}
