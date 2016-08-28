<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\ORM\TableRegistry;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public $paginate = [
        'limit' => 25,
        'order' => [
            'Users.created' => 'desc'
        ],
        'sortWhitelist' => [
            'name', 'role', 'created'
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function index()
    {
        $this->set([
            'pageTitle' => 'Manage Users',
            'users' => $this->paginate('Users')
        ]);
    }

    public function add()
    {
        $user = $this->Users->newEntity();

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['password'] = $this->request->data('new_password');
            $user = $this->Users->patchEntity($user, $this->request->data());
            $errors = $user->errors();
            if (empty($errors) && $this->Users->save($user)) {
                $this->Flash->success('User account created');
                return $this->redirect([
                    'prefix' => 'admin',
                    'action' => 'index'
                ]);
            } else {
                $this->Flash->error('There was an error creating this user\'s account. Please try again or contact an administrator for assistance.');
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
        $this->render('/Admin/Users/form');
    }

    public function edit($id = null)
    {
        $user = $this->Users->get($id);

        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->request->data('new_password') != '') {
                $this->request->data['password'] = $this->request->data('new_password');
            }
            $user = $this->Users->patchEntity($user, $this->request->data());
            $errors = $user->errors();
            if (empty($errors)) {
                $roleChanged = $user->dirty('role');
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
        $this->render('/Admin/Users/form');
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
     */
    public function editProfile($userId)
    {
        $user = $this->Users->get($userId, [
            'contain' => ['Tags', 'Pictures']
        ]);
        $picturesTable = TableRegistry::get('Pictures');
        $user->pictures = $picturesTable->moveMainToFront($user->pictures, $user->main_picture_id);
        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data(), [
                'fieldList' => ['profile', 'tags'],
                'associated' => ['Tags'],
                'onlyIds' => true
            ]);
            $errors = $user->errors();
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
    }
}
