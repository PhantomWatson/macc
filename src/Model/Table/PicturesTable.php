<?php
namespace App\Model\Table;

use App\Model\Entity\Picture;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
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

        $this->table('pictures');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'filename' => [
                'pathProcessor' => 'App\Media\PathProcessor',
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
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->add('is_primary', 'valid', ['rule' => 'boolean'])
            ->requirePresence('is_primary', 'create')
            ->notEmpty('is_primary');

        $validator
            ->requirePresence('filename', 'create')
            ->notEmpty('filename');

        $validator->provider('upload', \Josegonzalez\Upload\Validation\DefaultValidation::class);
        $fileUploaded = function ($context) {
            return !empty($context['data']['file']) && $context['data']['file']['error'] == UPLOAD_ERR_OK;
        };
        $validator
            ->add('file', 'fileUnderPhpSizeLimit', [
                'rule' => 'isUnderPhpSizeLimit',
                'message' => 'Sorry, this image exceeds the maximum filesize',
                'provider' => 'upload',
                'on' => $fileUploaded
            ])
            ->add('file', 'fileCompletedUpload', [
                'rule' => 'isCompletedUpload',
                'message' => 'This file could not be uploaded completely',
                'provider' => 'upload',
                'on' => $fileUploaded
            ])
            ->add('file', 'fileFileUpload', [
                'rule' => 'isFileUpload',
                'message' => 'No file was uploaded',
                'provider' => 'upload'
            ])->add('file', 'fileSuccessfulWrite', [
                'rule' => 'isSuccessfulWrite',
                'message' => 'There was an error saving the uploaded file',
                'provider' => 'upload',
                'on' => $fileUploaded
            ])->add('file', 'fileAboveMinHeight', [
                'rule' => ['isAboveMinHeight', 200],
                'message' => 'This image should at least be 200px high',
                'provider' => 'upload',
                'on' => $fileUploaded
            ])
            ->add('file', 'fileAboveMinWidth', [
                'rule' => ['isAboveMinWidth', 200],
                'message' => 'This image should at least be 200px wide',
                'provider' => 'upload',
                'on' => $fileUploaded
            ])
            ->add('file', 'isValidExtension', [
                'rule' => ['extension', ['jpg', 'jpeg', 'gif', 'png']],
                'message' => 'Sorry, your images need to have a filetype of .jpg, .png, or .gif',
                'on' => $fileUploaded
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
}