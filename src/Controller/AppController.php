<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\MailingList\MailingList;
use App\Model\Entity\User;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Recaptcha\Controller\Component\RecaptchaComponent;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 * @property RecaptchaComponent $Recaptcha
 */
class AppController extends Controller
{

    public $helpers = [
        'Form' => [
            'templates' => 'bootstrap_form'
        ]
    ];

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');
        $this->loadComponent('Paginator');

        $this->loadComponent('Cookie', [
            'encryption' => 'aes',
            'key' => Configure::read('cookie_key')
        ]);
        $this->Cookie->setConfig([
            'httpOnly' => true
        ]);
        $this->loadComponent('Recaptcha.Recaptcha');

        $this->loadComponent('Auth', [
            'loginAction' => [
                'prefix' => false,
                'controller' => 'Users',
                'action' => 'login'
            ],
            'logoutRedirect' => '/',
            'authenticate' => [
                'Form' => [
                    'fields' => ['username' => 'email']
                ],
                'CakeDC/Auth.RememberMe' => [
                    'fields' => ['username' => 'email'],
                    'Cookie' => ['name' => 'CookieAuth']
                ]
            ],
            'authorize' => ['Controller'],
            'flash' => [
                'params' => [
                    'class' => 'danger'
                ]
            ]
        ]);
        $this->Auth->deny();
        $errorMessage = $this->Auth->user() ?
            'Sorry, you are not authorized to access that page.'
            : 'Please log in before accessing that page.';
        $this->Auth->setConfig('authError', $errorMessage);

        if (Configure::read('forceSSL')) {
            $this->loadComponent('Security', ['blackHoleCallback' => 'forceSSL']);
            $this->Security->requireSecure();
            $this->Security->setConfig('validatePost', false);
        }
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->getType(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }

        $this->set('authUser', $this->Auth->user());
    }

    public function beforeFilter(Event $event)
    {
        if (! $this->Auth->user() && $this->Cookie->read('CookieAuth')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
            } else {
                $this->Cookie->delete('CookieAuth');
            }
        }
    }

    public function isAuthorized($user)
    {
        if (! isset($user['role'])) {
            return false;
        }

        // Admin can access every action
        if ($user['role'] === 'admin') {
            return true;
        }

        // Non-admin users can access any action not admin-prefixed
        $prefix = $this->request->getParam('prefix') ? $this->request->getParam('prefix') : null;
        return $prefix != 'admin';
    }

    /**
     * Redirect to HTTPS protocol
     */
    public function forceSSL()
    {
        return $this->redirect('https://'.env('SERVER_NAME').$this->request->getAttribute('here'));
    }

    /**
     * Returns TRUE if it appears that the site is being served from localhost
     *
     * @return boolean
     */
    protected function onLocalhost()
    {
        $whitelist = ['127.0.0.1', '::1'];
        return in_array(env('REMOTE_ADDR'), $whitelist);
    }

    /**
     * Processes a form with user registration info, returning either the registered user entity or FALSE
     *
     * Shows flash messages for errors, but does not show any messages for success
     *
     * @throws \Exception
     * @return bool|\Cake\Http\Response
     */
    protected function processRegister()
    {
        if (!$this->Recaptcha->verify()) {
            $this->Flash->error('There was an error verifying your reCAPTCHA response. Please try again.');

            return false;
        }

        $email = $this->request->getData('email');
        $email = trim($email);
        $email = strtolower($email);
        $data = $this->request->getData();
        $data['email'] = $email;
        $data['password'] = $data['new_password'];
        $data['role'] = 'user';

        /** @var User|bool $user */
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->newEntity();
        $user = $usersTable->patchEntity($user, $data, [
            'fieldList' => ['name', 'email', 'password', 'role']
        ]);

        if (!$usersTable->save($user)) {
            $adminEmail = Configure::read('admin_email');
            $this->Flash->error(
                'There was an error registering your account. ' .
                'Please correct any indicated errors and try again. ' .
                'For assistance, please contact <a href="mailto:' . $adminEmail . '">' . $adminEmail . '</a>.'
            );

            return false;
        }

        if ($this->request->getData('mailing_list')) {
            MailingList::addToList($user);
        }

        return $user;
    }

    /**
     * Returns a "for assistance..." string with a link to email the site administrator
     *
     * @return string
     */
    protected function getContactAdminMessage()
    {
        $adminEmail = Configure::read('admin_email');

        return 'For assistance, please contact <a href="mailto:' . $adminEmail . '">' . $adminEmail . '</a>.';
    }

    /**
     * Returns a redirect response to the login page, which will itself redirect to the current page by default
     *
     * @param string|null $redirect
     * @return \Cake\Http\Response
     */
    protected function redirectToLogin($redirect = null)
    {
        if (!$redirect) {
            $redirect = $this->request->getRequestTarget();
        }

        return $this->redirect([
            'controller' => 'Users',
            'action' => 'login',
            '?' => ['redirect' => $redirect]
        ]);
    }
}
