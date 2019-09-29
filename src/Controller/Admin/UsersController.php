<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\Entity\Logo;
use App\Model\Table\LogosTable;
use App\Model\Table\MembershipLevelsTable;
use App\Model\Table\PicturesTable;
use App\Model\Table\TagsTable;
use App\Model\Table\UsersTable;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Exception;

/**
 * Users Controller
 *
 * @property LogosTable $Logos
 * @property MembershipLevelsTable $MembershipLevels
 * @property TagsTable $Tags
 * @property UsersTable $Users
 */
class UsersController extends AppController
{
    use MailerAwareTrait;

    public $paginate = [
        'limit' => 25,
        'order' => [
            'Users.name' => 'asc'
        ],
        'sortWhitelist' => [
            'created',
            'name',
            'role',
            'Users.name',
            'Users.role',
            'Users.created'
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    /**
     * Page for listing user accounts
     *
     * @return void
     */
    public function index()
    {
        $this->set([
            'pageTitle' => 'Manage Users',
            'users' => $this->paginate('Users')
        ]);
    }

    /**
     * Page for manually adding a new user
     *
     * @return \Cake\Http\Response
     */
    public function add()
    {
        $user = $this->Users->newEntity();

        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->getData();
            if ($this->request->getData('new_password')) {
                $data['password'] = $data['new_password'];
            }
            $user = $this->Users->patchEntity($user, $data);
            $errors = $user->getErrors();
            if (empty($errors) && $this->Users->save($user)) {
                $this->Flash->success('User account created');

                $this->getMailer('Membership')
                    ->send('accountAddedByAdmin', [$user, $data['password']]);

                if ($this->request->getData('addMembership')) {
                    $this->Flash->set(
                        'Now, you can use this form to manually create a payment record reflecting that this ' .
                        'user has purchased a membership'
                    );

                    return $this->redirect([
                        'prefix' => 'admin',
                        'controller' => 'Payments',
                        'action' => 'add',
                        '?' => [
                            'u' => $user->id
                        ]
                    ]);
                }

                return $this->redirect([
                    'prefix' => 'admin',
                    'action' => 'index'
                ]);
            } else {
                $this->Flash->error(
                    'There was an error creating this user\'s account. ' .
                    'Please try again or contact an administrator for assistance.'
                );
            }
        }
        $this->set([
            'pageTitle' => 'Add User',
            'roles' => [
                'user' => 'User',
                'admin' => 'Admin'
            ],
            'user' => $user,
            'randomPassword' => $this->getRandomPassword()
        ]);

        return $this->render('/Admin/Users/form');
    }

    public function edit($id = null)
    {
        $user = $this->Users->get($id);

        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->getData();
            if ($data['new_password'] != '') {
                $data['password'] = $data['new_password'];
            }
            $user = $this->Users->patchEntity($user, $data);
            $errors = $user->getErrors();
            if (empty($errors)) {
                $roleChanged = $user->isDirty('role');
                if ($this->Users->save($user)) {
                    $msg = 'User info updated.';
                    if ($roleChanged) {
                        $msg .= ' The update to this user\'s <strong>role</strong> will take effect';
                        $msg .= ' the next time they manually log in or when their session automatically refreshes.';
                    }
                    $this->Flash->success($msg);
                    return $this->redirect([
                        'prefix' => 'admin',
                        'action' => 'index'
                    ]);
                }
            } else {
                $this->Flash->error('Please correct the indicated error(s)');
            }
        }
        $this->set([
            'pageTitle' => $user->name,
            'roles' => [
                'user' => 'User',
                'admin' => 'Admin'
            ],
            'user' => $user
        ]);

        return $this->render('/Admin/Users/form');
    }

    public function delete($id = null)
    {
        if (! $this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $user = $this->Users->get($id);

        if ($this->Users->delete($user)) {
            $this->Flash->success('User deleted');
        } else {
            $this->Flash->error('User was not deleted');
        }
        return $this->redirect([
            'prefix' => 'admin',
            'action' => 'index'
        ]);
    }

    /**
     * Renders a page showing email lists for various categories of users
     *
     * @return void
     */
    public function emailLists()
    {
        $emailLists = [];

        // All Members
        $allMembers = $this->Users->find('members')
            ->select(['id', 'email'])
            ->contain([
                'Memberships' => function (Query $q) {
                    return $q
                        ->orderDesc('Memberships.created')
                        ->contain(['MembershipLevels']);
                }
            ])
            ->order(['email' => 'ASC'])
            ->toArray();
        $emailLists['All Current Members'] = Hash::extract($allMembers, '{n}.email');

        // Members at specific levels
        $this->loadModel('MembershipLevels');
        $membershipLevels = $this->MembershipLevels->find()->all();
        foreach ($membershipLevels as $membershipLevel) {
            $key = sprintf(
                'Members (%s level)',
                $membershipLevel->name
            );
            foreach ($allMembers as $member) {
                $membershipLevelId = $member->memberships[0]->membership_level_id;
                if ($membershipLevelId == $membershipLevel->id) {
                    $emailLists[$key][] = $member->email;
                }
            }
        }

        $results = $this->Users->find('members')
            ->select(['id', 'email'])
            ->order(['email' => 'ASC'])
            ->toArray();
        $emailLists['All Current Members'] = Hash::extract($results, '{n}.email');

        // Non-members
        $memberIds = Hash::extract($results, '{n}.id');
        $results = $this->Users->find('all')
            ->where([
                function ($exp) use ($memberIds) {
                    /** @var QueryExpression $exp */

                    return $exp->notIn('id', $memberIds);
                }
            ])
            ->select(['id', 'email'])
            ->order(['email' => 'ASC'])
            ->toArray();
        $nonMembers = Hash::extract($results, '{n}.email');
        $emailLists['Users Without Memberships'] = $nonMembers;

        $this->set([
            'pageTitle' => 'Email Lists',
            'emailLists' => $emailLists
        ]);
    }

    /**
     * Page that lists the mailing addresses for all members (or optionally, all users)
     *
     * @return void
     */
    public function addresses()
    {
        $query = TableRegistry::getTableLocator()
            ->get('Users')
            ->find()
            ->select([
                'Users.id',
                'Users.name',
                'Users.address',
                'Users.city',
                'Users.state',
                'Users.zipcode'
            ])
            ->where([
                function (QueryExpression $exp) {
                    return $exp->notEq('address', '');
                }
            ])
            ->contain([
                'Memberships' => function (Query $q) {
                    return $q
                        ->select([
                            'Memberships.id',
                            'Memberships.user_id',
                            'Memberships.membership_level_id',
                            'MembershipLevels.name'
                        ])
                        ->where([
                            function (QueryExpression $exp) {
                                return $exp->gte('expires', date('Y-m-d H:i:s'));
                            }
                        ])
                        ->contain(['MembershipLevels'])
                        ->orderDesc('Memberships.created');
                },
            ])
            ->orderAsc('Users.name');

        if (!$this->request->getQuery('all')) {
            $query->find('members');
        }

        $this->set([
            'pageTitle' => 'Mailing Addresses',
            'users' => $query->all()
        ]);
    }

    /**
     * Returns a randomized password
     *
     * @return string
     */
    private function getRandomPassword()
    {
        $characters = str_shuffle('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789');

        return substr($characters, 0, 6);
    }

    /**
     * Form for updating user profile blurb
     *
     * @return void
     */
    public function updateBio($userId)
    {
        $user = $this->Users->get($userId);
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fields' => ['name', 'profile'],
            ]);
            $errors = $user->getErrors();
            if (empty($errors)) {
                if ($this->Users->save($user)) {
                    $this->Flash->success('Profile updated');
                } else {
                    $this->Flash->error('There was an error updating that profile');
                }
            } else {
                $this->Flash->error('Please correct the indicated error(s) before proceeding');
            }
        }
        $this->set([
            'pageTitle' => "Update $user->name's Bio",
            'user' => $user,
        ]);
    }

    /**
     * Form for updating user tags
     *
     * @return void
     */
    public function updateTags($userId)
    {
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
                } else {
                    $this->Flash->error('There was an error saving your tags');
                }
            } else {
                $this->Flash->error('Please correct the indicated error(s) before proceeding');
            }
        }
        $this->loadModel('Tags');
        $this->set([
            'pageTitle' => "Update $user->name's Tags",
            'tags' => $this->Tags->getThreaded(),
            'user' => $user,
        ]);
    }

    /**
     * Form for updating user pictures
     *
     * @param int $userId
     * @return void
     */
    public function updatePictures($userId)
    {
        $user = $this->Users->get($userId, [
            'contain' => ['Pictures']
        ]);
        /** @var PicturesTable $picturesTable */
        $picturesTable = TableRegistry::getTableLocator()->get('Pictures');
        $user->pictures = $picturesTable->moveMainToFront($user->pictures, $user->main_picture_id);
        $this->set([
            'pageTitle' => "Update $user->name's Pictures",
            'picLimit' => Configure::read('maxPicturesPerUser'),
            'user' => $user
        ]);
        $this->setUploadFilesizeLimit();
    }

    /**
     * Page for updating one's own contact info
     *
     * @param int $userId User ID
     * @return void
     * @throws Exception
     */
    public function updateContact($userId)
    {
        $user = $this->Users->get($userId);
        if ($this->request->is('put')) {
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
                }
            }
        }

        $this->request = $this->request->withData('current_password', null);

        $this->set([
            'pageTitle' => "Update $user->name's Contact Info",
            'user' => $user,
            'showPasswordField' => (bool)$user->getError('current_password')
        ]);

        return null;
    }

    /**
     * Page for updating a user's logo
     *
     * @param int $userId User ID
     * @return void
     */
    public function updateLogo($userId)
    {
        // Users can see this page if their most recent membership (expired or not) is at a high enough level
        $qualifies = UsersTable::qualifiesForLogo($userId);

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
            'pageTitle' => "Update $user->name's Logo",
            'qualifiesForLogo' => $qualifies,
            'user' => $user
        ]);
        $this->setUploadFilesizeLimit();
    }

    /**
     * Uploads a logo
     *
     * @return void
     */
    public function uploadLogo()
    {
        $this->set('_serialize', ['message', 'filepath']);

        if ($this->request->is('post')) {
            $this->loadModel('Logos');

            // Create new logo record
            /** @var Logo $logo */
            $logo = $this->Logos->newEntity([
                'user_id' => $this->request->getData('user_id'),
                'filename' => $this->request->getData('Filedata')
            ]);

            if ($this->Logos->save($logo)) {
                $logo->deleteOtherLogos();
                $this->set([
                    'message' => 'Logo uploaded',
                    'filepath' => $logo->filepath
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
     * Deletes all logos for the specified user and redirects to referrer
     *
     * @param int $userId User ID
     * @return Response
     */
    public function removeLogo($userId)
    {
        $this->loadModel('Logos');
        $this->Logos->deleteAll(['user_id' => $userId]);
        $dir = new Folder(WWW_ROOT . 'img' . DS . 'logos' . DS . $userId);
        foreach ($dir->find() as $file) {
            (new File($dir->pwd() . DS . $file))->delete();
        }
        $dir->delete();

        return $this->redirect($this->referer());
    }
}
