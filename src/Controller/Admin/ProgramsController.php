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
                $this->Flash->success('Program added');

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

        return $this->render('form');
    }

    /**
     * Edit method
     *
     * @param string|null $id Program id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $program = $this->Programs->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $program = $this->Programs->patchEntity($program, $this->request->getData());
            if ($this->Programs->save($program)) {
                $this->Flash->success('Program updated');

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(
                'The program could not be saved. Please correct any displayed errors and try again.'
            );
        }
        $this->set([
            'pageTitle' => 'Update Program',
            'program' => $program
        ]);

        return $this->render('form');
    }
}
