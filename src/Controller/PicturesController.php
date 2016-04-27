<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;

/**
 * Pictures Controller
 *
 * @property \App\Model\Table\PicturesTable $Pictures
 */
class PicturesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function add()
    {
        $this->viewBuilder()->layout('json');
        $picture = $this->Pictures->newEntity();
        if ($this->request->is('post')) {
            $this->request->data['filename'] = $this->request->data('Filedata');
            $this->request->data['is_primary'] = false;
            $this->request->data['user_id'] = $this->Auth->user('id');
            $picture = $this->Pictures->patchEntity($picture, $this->request->data);
            if ($picture->errors()) {
                $msg = 'There was an error uploading that picture. Please try again.';
                $msg .= '<br />Details: <pre>'.print_r($picture->errors(), true).'</pre>';
                throw new BadRequestException($msg);
            } elseif ($this->Pictures->save($picture)) {
                $message = 'Picture successfully uploaded';
                $this->set([
                    'message' => $message,
                    'picture' => $picture->filename
                ]);
            } else {
                $msg = 'There was an error uploading that picture. Please try again.';
                throw new InternalErrorException($msg);
            }
        } else {
            throw new BadRequestException('No picture was uploaded');
        }
        $this->set('_serialize', ['message', 'picture']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Picture id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $picture = $this->Pictures->get($id);
        if ($this->Pictures->delete($picture)) {
            $this->Flash->success(__('The picture has been deleted.'));
        } else {
            $this->Flash->error(__('The picture could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
