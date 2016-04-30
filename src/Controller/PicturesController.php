<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ForbiddenException;
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

        // Ensure user doesn't exceed picture limit
        $userId = $this->Auth->user('id');
        $currentCount = $this->Pictures->getCountForUser($userId);
        $maxPicturesPerUser = Configure::read('maxPicturesPerUser');
        if ($currentCount >= $maxPicturesPerUser) {
            $msg = 'Sorry, you\'ve reached your limit of '.$maxPicturesPerUser.__n(' picture', ' pictures', $maxPicturesPerUser);
            throw new ForbiddenException($msg);
        }

        $picture = $this->Pictures->newEntity();
        if ($this->request->is('post')) {
            $this->request->data['filename'] = $this->request->data('Filedata');
            $this->request->data['is_primary'] = false;
            $this->request->data['user_id'] = $userId;
            $picture = $this->Pictures->patchEntity($picture, $this->request->data);
            if ($picture->errors()) {
                $exceptionMsg = 'There was an error uploading that picture. Please try again.';
                $exceptionMsg .= '<ul>';
                foreach ($picture->errors() as $field => $errors) {
                    foreach ($errors as $label => $message) {
                        $exceptionMsg .= '<li>'.$message.'</li>';
                    }
                }
                $exceptionMsg .= '</ul>';
                throw new BadRequestException($exceptionMsg);
            } else {
                $picture = $this->Pictures->save($picture);
                if ($picture) {
                    $message = 'Picture successfully uploaded';
                    $this->set([
                        'message' => $message,
                        'picture' => $picture->filename,
                        'pictureId' => $picture->id
                    ]);
                } else {
                    $msg = 'There was an error uploading that picture. Please try again.';
                    throw new InternalErrorException($msg);
                }
            }
        } else {
            throw new BadRequestException('No picture was uploaded');
        }
        $this->set('_serialize', ['message', 'picture', 'pictureId']);
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
        $userId = $this->Auth->user('id');
        $ownerId = $picture->user_id;
        if ($userId != $ownerId) {
            throw new ForbiddenException('You cannot delete pictures that you are not the owner of.');
        }
        if ($this->Pictures->delete($picture)) {
            $message = 'The picture has been deleted.';
        } else {
            $message = 'The picture could not be deleted. Please, try again.';
        }
        $this->set('message', $message);
    }
}
