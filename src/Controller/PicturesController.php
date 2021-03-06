<?php
namespace App\Controller;

use App\Model\Entity\Picture;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;

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

    /**
     * Adds a picture to a user profile
     *
     * @throws \Aura\Intl\Exception
     * @return void
     */
    public function add()
    {
        $this->viewBuilder()->setLayout('json');

        // Ensure user doesn't exceed picture limit
        $userId = $this->Auth->user('id');
        $currentCount = $this->Pictures->getCountForUser($userId);
        $maxPicturesPerUser = Configure::read('maxPicturesPerUser');
        if ($currentCount >= $maxPicturesPerUser) {
            $msg = sprintf(
                'Sorry, you\'ve reached your limit of %s %s',
                $maxPicturesPerUser,
                __n(' picture', ' pictures', $maxPicturesPerUser)
            );
            throw new ForbiddenException($msg);
        }

        /** @var Picture $picture */
        $picture = $this->Pictures->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['filename'] = $this->request->getData('Filedata');
            $data['is_primary'] = false;
            $data['user_id'] = $userId;
            $picture = $this->Pictures->patchEntity($picture, $data);
            if ($picture->getErrors()) {
                throw new BadRequestException($picture->getUploadErrorList());
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
     * @return void
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

    public function makeMain($pictureId)
    {
        $userId = $this->Auth->user('id');
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get($userId);
        $user = $usersTable->patchEntity($user, ['main_picture_id' => $pictureId]);
        $usersTable->save($user);
    }
}
