<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Programs Controller
 *
 * @property \App\Model\Table\ProgramsTable $Programs
 *
 * @method \App\Model\Entity\Program[]|\Cake\Datasource\ResultSetInterface paginate($object = null, $settings = [])
 */
class ProgramsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->set([
            'pageTitle' => 'Programs',
            'programs' => $this->Programs
                ->find()
                ->orderAsc('name')

        ]);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $program = $this->Programs->newEntity();
        if ($this->request->is('post')) {
            $program = $this->Programs->patchEntity($program, $this->request->getData());
            if ($this->Programs->save($program)) {
                $this->Flash->success('The program has been saved.');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(
                'The program could not be saved. Please correct any displayed errors and try again.'
            );
        }
        $this->set([
            'pageTitle' => 'Add New Program',
            'program' => $program
        ]);

        return null;
    }
}
