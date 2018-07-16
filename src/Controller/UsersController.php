<?php
namespace App\Controller;

use App\Mailer\Mailer;
use App\MailingList\MailingList;
use App\Model\Table\PicturesTable;
use App\Model\Table\TagsTable;
use App\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Recaptcha\Controller\Component\RecaptchaComponent;

/**
 * Users Controller
 *
 * @property UsersTable $Users
 * @property RecaptchaComponent $Recaptcha
 * @property TagsTable $Tags
 */
class UsersController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        if ($this->request->action === 'register') {
            $this->loadComponent('Recaptcha.Recaptcha');
        }
        $this->Auth->allow([
            'forgotPassword',
            'login',
            'logout',
            'members',
            'register',
            'resetPassword',
            'view'
        ]);
    }

    public function register()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            if ($this->Recaptcha->verify()) {
                $email = $this->request->getData('email');
                $email = trim($email);
                $email = strtolower($email);
                $data = $this->request->getData();
                $data['email'] = $email;
                $data['password'] = $data['new_password'];
                $data['role'] = 'user';
                $user = $this->Users->patchEntity($user, $data, [
                    'fieldList' => ['name', 'email', 'password', 'role']
                ]);
                $errors = $user->getErrors();
                if (empty($errors)) {
                    $user = $this->Users->save($user);
                    if ($this->request->getData('mailing_list')) {
                        MailingList::addToList($user);
                    }
                    $this->Flash->success('Your account has been registered. You may now log in.');
                    return $this->redirect(['action' => 'login']);
                } else {
                    $this->Flash->error('There was an error registering your account. Please try again.');
                }
            } else {
                $this->Flash->error('There was an error verifying your reCAPTCHA response. Please try again.');
            }
        } else {
            $user['mailing_list'] = true;
        }

        /* So the password fields aren't filled out automatically when the user
         * is bounced back to the page by a validation error */
        $user['new_password'] = null;
        $user['confirm_password'] = null;

        $this->set([
            'pageTitle' => 'Register an Account',
            'user' => $user
        ]);
    }

    public function editProfile()
    {
        $userId = $this->Auth->user('id');
        $isCurrentMember = $this->Users->isCurrentMember($userId);
        if (! $isCurrentMember) {
            $hasExpiredMembership = $this->Users->hasExpiredMembership($userId);
            if ($hasExpiredMembership) {
                $this->Flash->error('Please renew your MACC membership before updating your member profile');
                return $this->redirect([
                    'controller' => 'Memberships',
                    'action' => 'myMembership'
                ]);
            } else {
                $this->Flash->error('Please purchase a MACC membership to start building your member profile');
                return $this->redirect([
                    'controller' => 'Memberships',
                    'action' => 'levels'
                ]);
            }
        }

        $user = $this->Users->get($userId, [
            'contain' => ['Tags', 'Pictures']
        ]);
        /** @var PicturesTable $picturesTable */
        $picturesTable = TableRegistry::getTableLocator()->get('Pictures');
        $user->pictures = $picturesTable->moveMainToFront($user->pictures, $user->main_picture_id);
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fieldList' => ['profile', 'tags'],
                'associated' => ['Tags'],
                'onlyIds' => true
            ]);
            $errors = $user->getErrors();
            if (empty($errors)) {
                if ($this->Users->save($user)) {
                    $this->Flash->success('Profile updated');
                } else {
                    $this->Flash->error('There was an error saving your profile');
                }
            } else {
                $this->Flash->error('Please correct the indicated error(s) before proceeding');
            }
        }
        $this->loadModel('Tags');
        $this->set([
            'pageTitle' => 'Update Profile',
            'tags' => $this->Tags->getThreaded(),
            'user' => $user,
            'picLimit' => Configure::read('maxPicturesPerUser')
        ]);
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Flash->success('You have been successfully logged in');
                $this->Auth->setUser($user);

                // Remember login information
                if ($this->request->getData('auto_login')) {
                    $this->Cookie->configKey('CookieAuth', [
                        'expires' => '+1 year',
                        'httpOnly' => true
                    ]);
                    $this->Cookie->write('CookieAuth', [
                        'email' => $this->request->getData('email'),
                        'password' => $this->request->getData('password')
                    ]);
                }

                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error('Email or password is incorrect');
            }
        } else {
            $user = $this->Users->newEntity();
            $user['auto_login'] = true;
        }
        $this->set([
            'pageTitle' => 'Log in',
            'user' => $user
        ]);
    }

    public function logout()
    {
        $this->Cookie->delete('CookieAuth');
        return $this->redirect($this->Auth->logout());
    }

    public function forgotPassword()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');
            $email = strtolower(trim($email));
            $adminEmail = Configure::read('admin_email');
            if (empty($email)) {
                $msg = 'Please enter the email address you registered with to have your password reset. ';
                $msg .= 'Email <a href="mailto:'.$adminEmail.'">'.$adminEmail.'</a> for assistance.';
                $this->Flash->error($msg);
            } else {
                $userId = $this->Users->getIdWithEmail($email);
                if ($userId) {
                    if (Mailer::sendPasswordResetEmail($userId)) {
                        $this->Flash->success('Success! You should be shortly receiving an email with a link to reset your password.');
                        $user['email'] = '';
                    } else {
                        $msg = 'There was an error sending your password-resetting email. ';
                        $msg .= 'Please try again, or email <a href="mailto:'.$adminEmail.'">'.$adminEmail.'</a> for assistance.';
                        $this->Flash->error($msg);
                    }
                } else {
                    $msg = 'We couldn\'t find an account registered with the email address <strong>'.$email.'</strong>. ';
                    $msg .= 'Please make sure you spelled it correctly, and email ';
                    $msg .= '<a href="mailto:'.$adminEmail.'">'.$adminEmail.'</a> if you need assistance.';
                    $this->Flash->error($msg);
                }
            }
        }
        $this->set([
            'pageTitle' => 'Forgot Password',
            'user' => $user
        ]);
    }

    public function resetPassword($userId = null, $timestamp = null, $hash = null)
    {
        if (! $userId || ! $timestamp && ! $hash) {
            throw new NotFoundException('Incomplete URL for password-resetting. Did you leave out part of the URL when you copied and pasted it?');
        }

        if (time() - $timestamp > 60 * 60 * 24) {
            throw new ForbiddenException('Sorry, that link has expired.');
        }

        $expectedHash = Mailer::getPasswordResetHash($userId, $timestamp);
        if ($hash != $expectedHash) {
            throw new ForbiddenException('Invalid security key');
        }

        $user = $this->Users->get($userId);
        $email = $user->email;

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            $data['password'] = $data['new_password'];
            $user = $this->Users->patchEntity($user, $data, [
                'fieldList' => ['password']
            ]);
            if ($this->Users->save($user)) {
                $this->Flash->success('Your password has been updated.');
                return $this->redirect(['action' => 'login']);
            }
        }

        $user['new_password'] = '';
        $user['confirm_password'] = '';

        $this->set([
            'email' => $email,
            'pageTitle' => 'Reset Password',
            'user' => $user
        ]);
    }

    /**
     * View method
     *
     * @param string|null $userId User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($userId = null)
    {
        // Bounce back to index if selected user is not a current member or the logged-in user
        $ownProfile = $this->Auth->user('id') == $userId;
        $isCurrentMember = $this->Users->isCurrentMember($userId);
        if (! ($isCurrentMember || $ownProfile)) {
            $this->Flash->error('Sorry, no current member of the Muncie Arts and Culture Council was found matching your request.');
            return $this->redirect(['action' => 'members']);
        }

        if ($ownProfile && ! $isCurrentMember) {
            $hasExpiredMembership = $this->Users->hasExpiredMembership($userId);
            if ($hasExpiredMembership) {
                $url = Router::url([
                    'controller' => 'Memberships',
                    'action' => 'myMembership'
                ]);
                $msg = 'Sorry, your member profile can\'t be accessed until you ' .
                    '<a href="'.$url.'">renew your membership</a>';
            } else {
                $url = Router::url([
                    'controller' => 'Memberships',
                    'action' => 'levels'
                ]);
                $msg = 'Sorry, your member profile can\'t be accessed until you ' .
                    '<a href="'.$url.'">purchase a membership</a>';
            }
            $this->Flash->error($msg);
            return $this->redirect(['action' => 'members']);
        }

        $user = $this->Users->get($userId, [
            'contain' => [
                'Tags' => function ($q) {
                    /** @var Query $q */

                    return $q->order(['name' => 'ASC']);
                },
                'Pictures'
            ]
        ]);
        $this->set([
            'pageTitle' => $user->name,
            'user' => $user,
            'mainPicture' => [
                'fullsize' => $user->main_picture_fullsize,
                'thumb' => $user->main_picture_thumb
            ],
            'ownProfile' => $ownProfile
        ]);
    }

    public function changePassword()
    {
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId);
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->getData();
            $data['password'] = $data['new_password'];
            $user = $this->Users->patchEntity($user, $data, [
                'fieldList' => ['password']
            ]);
            if ($this->Users->save($user)) {
                $this->Flash->success('Your password has been updated');

                // If user logs in via cookie, update cookie login credentials
                if ($this->Cookie->read('CookieAuth')) {
                    $this->Cookie->write('CookieAuth.password', $this->request->getData('new_password'));
                }
            }
        }

        $user['new_password'] = '';
        $user['confirm_password'] = '';

        $this->set([
            'pageTitle' => 'Change Password',
            'user' => $user
        ]);
    }

    public function members()
    {
        $query = $this->Users->find('members')
            ->select(['id', 'name', 'slug', 'main_picture_id'])
            ->contain([
                'Tags' => function ($q) {
                    /** @var Query $q */

                    return $q->select(['id', 'name', 'slug']);
                },
                'Pictures' => function ($q) {
                    /** @var Query $q */

                    return $q->select(['id', 'user_id', 'filename']);
                }
            ])
            ->order(['Users.name' => 'ASC']);
        $this->paginate['limit'] = 20;
        $members = $this->paginate($query);

        foreach ($members as $member) {
            $member->main_picture_thumbnail = false;
            $member->main_picture_fullsize = false;
            if ($member->main_picture_id) {
                foreach ($member->pictures as $picture) {
                    if ($picture['id'] == $member->main_picture_id) {
                        $member->main_picture_fullsize = $picture->filename;
                        $member->main_picture_thumbnail = $picture->thumbnail_filename;
                    }
                }
            }
        }

        $this->set([
            'members' => $members,
            'pageTitle' => 'Members'
        ]);
    }

    public function account()
    {
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId);
        if ($this->request->is('put')) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fieldList' => ['name', 'email']
            ]);
            $errors = $user->getErrors();
            if (empty($errors)) {
                $this->Users->save($user);
                $this->Flash->success('Account info updated');

                // If user logs in via cookie, update cookie login credentials
                if ($this->Cookie->read('CookieAuth')) {
                    $this->Cookie->write('CookieAuth.email', $this->request->getData('email'));
                }
            }
        }
        $this->set([
            'user' => $user,
            'pageTitle' => 'Edit Account Info'
        ]);
    }
}
