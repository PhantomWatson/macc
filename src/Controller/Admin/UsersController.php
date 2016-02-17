<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public function index()
    {
        $users = $this->Users->find('all')
            ->order(['Users.name' => 'ASC']);

        $this->set([
            'pageTitle' => 'Manage Users',
            'users' => $users
        ]);
    }
}
