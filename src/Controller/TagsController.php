<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Network\Exception\NotFoundException;

/**
 * Tags Controller
 *
 * @property \App\Model\Table\TagsTable $Tags
 */
class TagsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow([
            'index',
            'view'
        ]);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $tags = $this->Tags->find('all')
            ->find('forMembers')
            ->select(['id', 'name', 'slug'])
            ->order(['Tags.name' => 'ASC']);

        $this->set([
            'pageTitle' => 'Art Tags',
            'tags' => $tags
        ]);
    }

    /**
     * View method
     *
     * @param string $slug
     * @return \Cake\Network\Response
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function view($slug = null)
    {
        $tag = $this->Tags->find('slug', [
            'slug' => $slug,
            'slugField' => 'Tags.slug'
        ])->contain([
            'Users' => function ($q) {
                return $q->find('members')->select(['id', 'name', 'slug']);
            }
        ]);

        if ($tag->isEmpty()) {
            throw new NotFoundException('Sorry, we couldn\'t find a "'.str_replace('-', ' ', $slug).'" tag');
        }

        $tag = $tag->first();

        $this->set([
            'pageTitle' => ucwords($tag->name),
            'tag' => $tag
        ]);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tag = $this->Tags->newEntity();
        if ($this->request->is('post')) {
            $tag = $this->Tags->patchEntity($tag, $this->request->data);
            if ($this->Tags->save($tag)) {
                $this->Flash->success(__('The tag has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The tag could not be saved. Please, try again.'));
            }
        }
        $parentTags = $this->Tags->ParentTags->find('list', ['limit' => 200]);
        $users = $this->Tags->Users->find('list', ['limit' => 200]);
        $this->set(compact('tag', 'parentTags', 'users'));
        $this->set('_serialize', ['tag']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Tag id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tag = $this->Tags->get($id, [
            'contain' => ['Users']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tag = $this->Tags->patchEntity($tag, $this->request->data);
            if ($this->Tags->save($tag)) {
                $this->Flash->success(__('The tag has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The tag could not be saved. Please, try again.'));
            }
        }
        $parentTags = $this->Tags->ParentTags->find('list', ['limit' => 200]);
        $users = $this->Tags->Users->find('list', ['limit' => 200]);
        $this->set(compact('tag', 'parentTags', 'users'));
        $this->set('_serialize', ['tag']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Tag id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tag = $this->Tags->get($id);
        if ($this->Tags->delete($tag)) {
            $this->Flash->success(__('The tag has been deleted.'));
        } else {
            $this->Flash->error(__('The tag could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
