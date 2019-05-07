<?php
namespace App\Model\Table;

use App\Media\ProfileImgTransformer;
use App\Model\Entity\Picture;
use ArrayObject;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Pictures Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 */
class PicturesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('pictures');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'filename' => [
                'pathProcessor' => 'App\Media\PathProcessor',
                'transformer' => 'App\Media\ProfileImgTransformer',
                'path' => 'webroot{DS}img{DS}members{DS}{user_id}{DS}',
                'keepFilesOnDelete' => false
            ]
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric']);

        $validator
            ->add('is_primary', 'valid', ['rule' => 'boolean'])
            ->requirePresence('is_primary', 'create');

        $validator
            ->requirePresence('filename', 'create')
            ->allowEmptyString('filename', false);

        $validator->setProvider('upload', \Josegonzalez\Upload\Validation\DefaultValidation::class);
        $validator
            ->add('filename', 'isValidExtension', [
                'rule' => ['extension', ['jpg', 'jpeg', 'gif', 'png']],
                'message' => 'Sorry, your images need to have a filetype of .jpg, .png, or .gif',
                'last' => true
            ])
            ->add('filename', 'fileUnderPhpSizeLimit', [
                'rule' => 'isUnderPhpSizeLimit',
                'message' => 'Sorry, this image exceeds the maximum filesize',
                'provider' => 'upload',
                'last' => true
            ])
            ->add('filename', 'fileCompletedUpload', [
                'rule' => 'isCompletedUpload',
                'message' => 'This file could not be uploaded completely',
                'provider' => 'upload',
                'last' => true
            ])
            ->add('filename', 'fileFileUpload', [
                'rule' => 'isFileUpload',
                'message' => 'No file was uploaded',
                'provider' => 'upload',
                'last' => true
            ])
            ->add('filename', 'fileSuccessfulWrite', [
                'rule' => 'isSuccessfulWrite',
                'message' => 'There was an error saving the uploaded file',
                'provider' => 'upload',
                'last' => true
            ])
            ->add('filename', 'fileAboveMinHeight', [
                'rule' => ['isAboveMinHeight', 200],
                'message' => 'This image should at least be 200px high',
                'provider' => 'upload',
            ])
            ->add('filename', 'fileAboveMinWidth', [
                'rule' => ['isAboveMinWidth', 200],
                'message' => 'This image should at least be 200px wide',
                'provider' => 'upload',
            ]);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }

    /**
     * Deletes the generated thumbnail after a picture record is deleted
     * and sets Users.main_picture_id to null if necessary
     *
     * josegonzalez/cakephp-upload plugin already takes care of deleting
     * the file for the full-size picture
     *
     * @param \Cake\Event\Event $event The afterDelete event that was fired
     * @param \App\Model\Entity\Picture $entity The entity that was deleted
     * @param \ArrayObject $options the options passed to the delete method
     * @return void|false
     */
    public function afterDelete(Event $event, Picture $entity, ArrayObject $options)
    {
        $fullsizeFilename = $entity->filename;
        $thumbFilename = ProfileImgTransformer::generateThumbnailFilename($fullsizeFilename);
        $file = new File(WWW_ROOT.'img'.DS.'members'.DS.$entity->user_id.DS.$thumbFilename);
        $file->delete();

        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get($entity->user_id);
        if ($user->main_picture_id == $entity->id) {
            $user->main_picture_id = null;
            $usersTable->save($user);
        }
    }

    /**
     * Returns number of pictures associated with specified user
     *
     * @param int $userId
     * @return int
     */
    public function getCountForUser($userId)
    {
        return $this->find('all')
            ->where(['user_id' => $userId])
            ->count();
    }

    /**
     * Takes an array of Picture entities and moves the main picture
     * (if it's in this array) to the front
     *
     * @param array $pictures
     * @param int|null $mainPictureId
     * @return array
     */
    public function moveMainToFront($pictures, $mainPictureId)
    {
        if (! $mainPictureId) {
            return $pictures;
        }

        $main = null;
        foreach ($pictures as $n => $picture) {
            if ($picture->id == $mainPictureId) {
                $main = $picture;
                unset($pictures[$n]);
                break;
            }
        }
        if ($main) {
            array_unshift($pictures, $main);
        }
        return $pictures;
    }

    /**
     * Automatically sets a picture to be the user's main picture if it's their only picture
     *
     * @param Event $event
     * @param Picture $entity
     * @param ArrayObject $options
     * @return void
     */
    public function afterSave(Event $event, Picture $entity, ArrayObject $options)
    {
        $userId = $entity->user_id;
        $count = $this->find('all')
            ->where(['user_id' => $userId])
            ->count();
        if ($count === 1) {
            $pictureId = $entity->id;
            $usersTable = TableRegistry::getTableLocator()->get('Users');
            $user = $usersTable->get($userId);
            $user->main_picture_id = $pictureId;
            $usersTable->save($user);
        }
    }
}
