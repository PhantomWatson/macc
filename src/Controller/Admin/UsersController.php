<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\Table\PicturesTable;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Response;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\TagsTable $Tags
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
            $data['password'] = $data['new_password'];
            $user = $this->Users->patchEntity($user, $data);
            $errors = $user->getErrors();
            if (empty($errors) && $this->Users->save($user)) {
                $this->Flash->success('User account created');

                $this->getMailer('Membership')
                    ->send('accountAddedByAdmin', [$user, $data['new_password']]);

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
            'user' => $user
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
     * Method for /admin/users/edit-profile/$userId
     *
     * Allows admins to edit users' profiles
     *
     * @param int $userId
     * @return Response|null
     */
    public function editProfile($userId)
    {
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
                    return $this->redirect([
                        'prefix' => 'admin',
                        'controller' => 'Users',
                        'action' => 'index'
                    ]);
                } else {
                    $this->Flash->error('There was an error saving that profile');
                }
            } else {
                $this->Flash->error('Please correct the indicated error(s) before proceeding');
            }
        }

        $isCurrentMember = $this->Users->isCurrentMember($userId);
        if (! $isCurrentMember) {
            $this->Flash->set('Note: This user is not currently a member.');
        }

        $this->loadModel('Tags');
        $this->set([
            'pageTitle' => 'Update ' . $user->name . '\'s Profile',
            'tags' => $this->Tags->getThreaded(),
            'user' => $user,
            'picLimit' => Configure::read('maxPicturesPerUser')
        ]);

        return null;
    }

    public function emailLists()
    {
        $emailLists = [];

        $results = $this->Users->find('members')
            ->select(['id', 'email'])
            ->order(['email' => 'ASC'])
            ->toArray();
        $emailLists['Current Members'] = Hash::extract($results, '{n}.email');

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
}
