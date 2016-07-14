<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Network\Exception\MethodNotAllowedException;

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
}
