<?php
namespace App\Controller;

use App\Mailer\Mailer;
use App\Model\Entity\Membership;
use App\Model\Entity\User;
use App\Model\Table\PicturesTable;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Recaptcha\Controller\Component\RecaptchaComponent;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property RecaptchaComponent $Recaptcha
 * @property \App\Model\Table\TagsTable $Tags
 */
class UsersController extends AppController
{

    public function initialize()
    {
        parent::initialize();
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

    /**
     * Registers a user
     *
     * @return Response|null
     * @throws \Exception
     */
    public function register()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');
            $email = trim($email);
            $email = strtolower($email);
            if ($this->Users->exists(['email' => $email])) {
                $this->Flash->set(
                    'Your email address has already been used to register an account. Please log in.'
                );
                return $this->redirectToLogin();
            }

            /** @var User|bool $result */
            $result = $this->processRegister();
            if ($result) {
                $password = $this->request->getData('new_password');
                $this->request = $this->request->withData('password', $password);

                // Log user in
                if ($this->Auth->identify()) {
                    $this->Auth->setUser($result->toArray());
                    $this->Flash->success(
                        'Your new website account has been created and you have been logged in.'
                    );
                    $userId = $result->id;
                    if (!$this->Users->hasMembership($userId)) {
                        $this->Flash->set(
                            'Check out the MACC membership options available below ' .
                            'and consider becoming a member today.'
                        );
                    }

                    return $this->redirect('/');

                // Prompt them to manually log in
                } else {
                    $this->Flash->error(
                        'There was an error automatically logging you in. Please manually log in to proceed.'
                    );

                    return $this->redirectToLogin('/');
                }
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

        return null;
    }

    /**
     * Shows a "your profile will not be available to view" flash message if appropriate
     *
     * @return void
     */
    private function setNonMemberAlert()
    {
        $userId = $this->Auth->user('id');
        $isCurrentMember = $this->Users->isCurrentMember($userId);
        if ($isCurrentMember) {
            $this->set('profileUnavailableMsg', null);

            return;
        }

        $hasExpiredMembership = $this->Users->hasExpiredMembership($userId);
        $action = $hasExpiredMembership ? 'renew your membership' : 'purchase a membership';
        $this->set(
            'profileUnavailableMsg',
            'Your profile will not be available to view on the MACC website until you ' . $action
        );
    }

    /**
     * Form for updating user profile blurb
     *
     * @return Response|null
     */
    public function myBio()
    {
        $this->setNonMemberAlert();
        $userId = $this->Auth->user('id');

        $user = $this->Users->get($userId);
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fieldList' => ['name', 'profile'],
            ]);
            $errors = $user->getErrors();
            if (empty($errors)) {
                if ($this->Users->save($user)) {
                    $this->Flash->success('Profile updated');
                    if ($this->request->getQuery('flow')) {
                        return $this->redirect([
                            'action' => 'myTags',
                            '?' => ['flow' => 1]
                        ]);
                    }
                } else {
                    $this->Flash->error('There was an error saving your profile');
                }
            } else {
                $this->Flash->error('Please correct the indicated error(s) before proceeding');
            }
        }
        $this->set([
            'pageTitle' => 'My Bio',
            'qualifiesForLogo' => $this->qualifiesForLogo(),
            'user' => $user,
        ]);

        return null;
    }

    /**
     * Form for updating user tags
     *
     * @return Response|null
     */
    public function myTags()
    {
        $this->setNonMemberAlert();
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId, [
            'contain' => ['Tags']
        ]);
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fieldList' => ['tags'],
                'associated' => ['Tags'],
                'onlyIds' => true
            ]);
            $errors = $user->getErrors();
            if (empty($errors)) {
                if ($this->Users->save($user)) {
                    $this->Flash->success('Tags updated');
                    if ($this->request->getQuery('flow')) {
                        return $this->redirect([
                            'action' => 'myPictures',
                            '?' => ['flow' => 1]
                        ]);
                    }
                } else {
                    $this->Flash->error('There was an error saving your tags');
                }
            } else {
                $this->Flash->error('Please correct the indicated error(s) before proceeding');
            }
        }
        $this->loadModel('Tags');
        $this->set([
            'pageTitle' => 'My Tags',
            'qualifiesForLogo' => $this->qualifiesForLogo(),
            'tags' => $this->Tags->getThreaded(),
            'user' => $user,
        ]);

        return null;
    }

    /**
     * Form for updating user pictures
     *
     * @return void
     */
    public function myPictures()
    {
        $this->setNonMemberAlert();
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId, [
            'contain' => ['Pictures']
        ]);
        /** @var PicturesTable $picturesTable */
        $picturesTable = TableRegistry::getTableLocator()->get('Pictures');
        $user->pictures = $picturesTable->moveMainToFront($user->pictures, $user->main_picture_id);
        $this->set([
            'pageTitle' => 'My Pictures',
            'picLimit' => Configure::read('maxPicturesPerUser'),
            'qualifiesForLogo' => $this->qualifiesForLogo(),
            'user' => $user
        ]);
    }

    /**
     * User login page
     *
     * @return \Cake\Http\Response|null
     */
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
                        'id' => $user['id'],
                        'user_agent' => $this->request->getHeaderLine('User-Agent')
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

        return null;
    }

    /**
     * Logout page and redirect
     *
     * @return \Cake\Http\Response
     */
    public function logout()
    {
        $this->Cookie->delete('CookieAuth');

        return $this->redirect($this->Auth->logout());
    }

    /**
     * Page for entering an email address to have a password-resetting email sent to
     *
     * @return void
     */
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
                        $this->Flash->success(
                            'Success! You should be shortly receiving an email ' .
                            'with a link to reset your password.'
                        );
                        $user['email'] = '';
                    } else {
                        $msg = 'There was an error sending your password-resetting email. Please try again, or email ' .
                            '<a href="mailto:'.$adminEmail.'">'.$adminEmail.'</a> for assistance.';
                        $this->Flash->error($msg);
                    }
                } else {
                    $msg = 'We couldn\'t find an account registered with the email address ' .
                        '<strong>'.$email.'</strong>. Please make sure you spelled it correctly, and email ' .
                        '<a href="mailto:'.$adminEmail.'">'.$adminEmail.'</a> if you need assistance.';
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
            throw new NotFoundException(
                'Incomplete URL for password-resetting. ' .
                'Did you leave out part of the URL when you copied and pasted it?'
            );
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

        return null;
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
            $this->Flash->error(
                'Sorry, no current member of the Muncie Arts and Culture Council ' .
                'was found matching your request.'
            );
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

        return null;
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

    /**
     * Page for updating one's own contact info
     *
     * @return Response|null
     */
    public function myContact()
    {
        $this->setNonMemberAlert();
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId);
        if ($this->request->is('put')) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fieldList' => [
                    'email',
                    'address',
                    'city',
                    'state',
                    'zipcode'
                ]
            ]);
            $errors = $user->getErrors();
            if (empty($errors)) {
                if ($this->Users->save($user)) {
                    // If user logs in via cookie, update cookie login credentials
                    if ($this->Cookie->read('CookieAuth')) {
                        $this->Cookie->write('CookieAuth.email', $this->request->getData('email'));
                    }

                    $this->Flash->success('Contact info updated');
                    $this->Flash->set(
                        'Please review your member profile to make sure your information is complete and accurate'
                    );
                    if ($this->request->getQuery('flow')) {
                        return $this->redirect([
                            'controller' => 'Users',
                            'action' => 'view',
                            $this->Auth->user('id'),
                            $this->Auth->user('slug')
                        ]);
                    }
                }
            }
        }
        $this->set([
            'pageTitle' => 'My Contact Info',
            'qualifiesForLogo' => $this->qualifiesForLogo(),
            'user' => $user
        ]);

        return null;
    }

    public function myLogo()
    {
        $qualifies = $this->qualifiesForLogo();
        if (!$qualifies) {
            $this->Flash->error(
                'Sorry, you\'ll need to purchase a membership at the Ambassador or Arts Hero level in order ' .
                'to upload a logo to be displayed on the MACC website.'
            );
            return $this->redirect('/');
        }

        $this->setNonMemberAlert();
        $user = $this->Users->get($this->Auth->user('id'), [
            'contain' => []
        ]);

        $this->set([
            'pageTitle' => 'My Logo',
            'qualifiesForLogo' => $qualifies,
            'user' => $user
        ]);

        return null;
    }

    /**
     * Returns TRUE if the current user qualifies for uploading a logo
     *
     * @return bool
     */
    private function qualifiesForLogo()
    {
        $userId = $this->Auth->user('id');

        if (!$userId) {
            return false;
        }

        /** @var Membership $membership */
        $membership = TableRegistry::getTableLocator()
            ->get('Memberships')
            ->getCurrentMembership($userId);

        if (!$membership) {
            return false;
        }

        return Membership::qualifiesForLogo($membership->membership_level_id);
    }
}
