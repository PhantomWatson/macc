<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Model\Entity\Picture;
use App\Model\Table\PicturesTable;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;
use Exception;

/**
 * Pictures Controller
 *
 * @property PicturesTable $Pictures
 */
class PicturesController extends AppController
{
    /**
     * Initialize method
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Adds a picture to a user profile
     *
     * @return void
     * @throws ForbiddenException
     */
    public function add()
    {
        $this->viewBuilder()->setLayout('json');
        $data = $this->request->getData();

        // Ensure the picture limit isn't being exceeded for this user
        $userId = $data['user_id'];
        $currentCount = $this->Pictures->getCountForUser($userId);
        $maxPicturesPerUser = Configure::read('maxPicturesPerUser');
        if ($currentCount >= $maxPicturesPerUser) {
            $msg = sprintf(
                'Sorry, that user is at their limit of %s %s. ' .
                    'One or more pictures will need to be deleted before adding more.',
                $maxPicturesPerUser,
                __n(' picture', ' pictures', $maxPicturesPerUser)
            );
            throw new ForbiddenException($msg);
        }

        /** @var Picture $picture */
        $picture = $this->Pictures->newEntity();
        if ($this->request->is('post')) {
            $data['filename'] = $data['Filedata'];
            $data['is_primary'] = false;
            $data['user_id'] = $userId;
            $picture = $this->Pictures->patchEntity($picture, $data);
            if ($picture->getErrors()) {
                $exceptionMsg = 'There was an error uploading that picture. Please try again.';
                $exceptionMsg .= '<ul>';
                foreach ($picture->getErrors() as $field => $errors) {
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
     * @param string|null $id Picture ID
     * @return void
     * @throws RecordNotFoundException
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $picture = $this->Pictures->get($id);
        $success = (bool)$this->Pictures->delete($picture);
        $message = $success ? 'The picture has been deleted.' : 'The picture could not be deleted. Please try again.';
        $this->set('message', $message);
    }

    /**
     * Makes the specified picture the main picture for the specified user
     *
     * @param int $userId ID of user to make this the main picture for
     * @param int $pictureId Picture ID
     * @return void
     */
    public function makeMain($userId, $pictureId)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get($userId);
        $user = $usersTable->patchEntity($user, ['main_picture_id' => $pictureId]);
        $success = (bool)$usersTable->save($user);
        $this->set('success', $success);
    }
}
