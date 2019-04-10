<?php
namespace App\Controller;

use App\Mailer\Mailer;
use App\Model\Entity\Membership;
use App\Model\Entity\User;
use App\Model\Table\LogosTable;
use App\Model\Table\PicturesTable;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Recaptcha\Controller\Component\RecaptchaComponent;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property RecaptchaComponent $Recaptcha
 * @property \App\Model\Table\TagsTable $Tags
 * @property LogosTable $Logos
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

            /** @var User $user */
            list($success, $user) = $this->processRegister();
            if ($success) {
                $password = $this->request->getData('new_password');
                $this->request = $this->request->withData('password', $password);

                // Log user in
                if ($this->Auth->identify()) {
                    $this->Auth->setUser($user->toArray());
                    $this->Flash->success(
                        'Your new website account has been created and you have been logged in.'
                    );
                    $userId = $user->id;
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
                'fields' => ['name', 'profile'],
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
                'fields' => ['tags'],
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
        $this->setUploadFilesizeLimit();
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
                'fields' => ['password']
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

    /**
     * Page for changing the user's password
     *
     * @return void
     */
    public function changePassword()
    {
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId);
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->getData();
            $data['password'] = $data['new_password'];
            $user = $this->Users->patchEntity($user, $data, [
                'fields' => ['password']
            ]);
            if ($this->Users->save($user)) {
                $this->Flash->success('Your password has been updated');

                // If user logs in via cookie, update cookie login credentials
                if ($this->Cookie->read('CookieAuth')) {
                    $this->Cookie->write('CookieAuth.password', $this->request->getData('new_password'));
                }
            }
        }

        $this->request = $this->request->withData('current_password', '');
        $this->request = $this->request->withData('new_password', '');
        $this->request = $this->request->withData('confirm_password', '');

        $this->set([
            'pageTitle' => 'Change Password',
            'user' => $user
        ]);
    }

    /**
     * A page listing all current members
     *
     * @return void
     */
    public function members()
    {
        $displayedTagLimit = 10;
        $members = $this->Users->find('members')
            ->select(['id', 'name', 'slug', 'main_picture_id'])
            ->contain([
                'Tags' => function (Query $q) {
                    return $q->select(['id', 'name', 'slug']);
                },
                'Pictures' => function (Query $q) {
                    return $q->select(['id', 'user_id', 'filename']);
                }
            ])
            ->order(['Users.name' => 'ASC'])
            ->all();

        foreach ($members as $member) {
            $member->main_picture_thumbnail = false;
            $member->main_picture_fullsize = false;
            if (!$member->main_picture_id) {
                continue;
            }
            foreach ($member->pictures as $picture) {
                if ($picture['id'] != $member->main_picture_id) {
                    continue;
                }
                $member->main_picture_fullsize = $picture->filename;
                $member->main_picture_thumbnail = $picture->thumbnail_filename;
            }
            $member->tag_list = Hash::extract($member->tags, '{n}.name');
            shuffle($member->tag_list);
            $member->tag_list = array_slice($member->tag_list, 0, $displayedTagLimit);
            $member->tag_list = implode(', ', $member->tag_list);
            $totalTagCount = count($member->tags);
            $member->more_tags = ($totalTagCount > $displayedTagLimit) ? ($totalTagCount - $displayedTagLimit) : null;
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
     * @throws \Exception
     */
    public function myContact()
    {
        $this->setNonMemberAlert();
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId);
        if ($this->request->is('put')) {
            $emailChanged = $this->request->getData('email') != $user->email;
            if (!$emailChanged) {
                $this->request = $this->request->withData('current_password', null);
            }

            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fields' => [
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

        $this->request = $this->request->withData('current_password', null);

        $this->set([
            'pageTitle' => 'My Contact Info',
            'qualifiesForLogo' => $this->qualifiesForLogo(),
            'user' => $user,
            'showPasswordField' => (bool)$user->getError('current_password')
        ]);

        return null;
    }

    public function myLogo()
    {
        // Users can see this page if their most recent membership (expired or not) is at a high enough level
        $qualifies = $this->qualifiesForLogo();

        $this->setNonMemberAlert();
        $userId = $this->Auth->user('id');
        $user = $this->Users->get($userId, [
            'contain' => []
        ]);

        $this->loadModel('Logos');
        $logo = $this->Logos
            ->find()
            ->where(['user_id' => $userId])
            ->first();
        $logoPath = $logo
            ? sprintf(
                '/img/logos/%s/%s',
                $logo->user_id,
                $logo->filename
            )
            : null;

        // Get name of most recently purchased member level
        $membership = TableRegistry::getTableLocator()
            ->get('Memberships')
            ->getCurrentMembership($userId);
        $memberLevelName = $membership ? $membership->membership_level->name : null;

        $this->set([
            'logoPath' => $logoPath,
            'memberLevelName' => $memberLevelName,
            'pageTitle' => 'My Logo',
            'qualifiesForLogo' => $qualifies,
            'user' => $user
        ]);
        $this->setUploadFilesizeLimit();

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

    /**
     * Uploads a logo
     *
     * @return void
     */
    public function uploadLogo()
    {
        if (!$this->qualifiesForLogo()) {
            $this->set([
                'message' => 'Error uploading logo: No current membership at qualifying level'
            ]);
            $this->response = $this->response->withStatus(500);

            return;
        }

        $this->viewBuilder()->setLayout('json');
        $this->set('_serialize', ['message', 'filepath']);

        if ($this->request->is('post')) {
            $userId = $this->Auth->user('id');
            $filename = $this->request->getData('Filedata');
            $this->loadModel('Logos');

            // Create new logo record
            $logo = $this->Logos->newEntity([
                'user_id' => $userId,
                'filename' => $filename
            ]);

            if ($this->Logos->save($logo)) {
                // Delete any previous logo files
                $dir = new Folder(WWW_ROOT . 'img' . DS . 'logos' . DS . $userId);
                $files = $dir->find();
                foreach ($files as $file) {
                    if ($file != $logo->filename) {
                        (new File($dir->pwd() . DS . $file))->delete();
                    }
                }

                // Delete any previous records
                $this->Logos->deleteAll([
                    'user_id' => $userId,
                    'id !=' => $logo->id
                ]);

                $this->set([
                    'message' => 'Logo uploaded',
                    'filepath' => sprintf(
                        '/img/logos/%s/%s',
                        $userId,
                        $logo->filename
                    )
                ]);

                return;
            }

            // Error
            $errorDetails = print_r(array_values($logo->getErrors()), true);
            $this->set([
                'message' => 'Error uploading logo. Details: ' . $errorDetails
            ]);
            $this->response = $this->response->withStatus(500);
        } else {
            throw new BadRequestException('No picture was uploaded');
        }
    }

    /**
     * Deletes all logos for the current user and redirects to referrer
     *
     * @return Response
     */
    public function removeLogo()
    {
        $this->loadModel('Logos');
        $userId = $this->Auth->user('id');
        $this->Logos->deleteAll(['user_id' => $userId]);
        $dir = new Folder(WWW_ROOT . 'img' . DS . 'logos' . DS . $userId);
        foreach ($dir->find() as $file) {
            (new File($dir->pwd() . DS . $file))->delete();
        }
        $dir->delete();

        return $this->redirect($this->referer());
    }

    /**
     * Sets the $manualFilesizeLimit view variable
     *
     * @return void
     */
    private function setUploadFilesizeLimit()
    {
        $uploadMax = ini_get('upload_max_filesize');
        $postMax = ini_get('post_max_size');
        $serverFilesizeLimit = min($uploadMax, $postMax);
        $this->set('manualFilesizeLimit', min('10M', $serverFilesizeLimit));
    }
}
