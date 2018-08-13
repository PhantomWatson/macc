<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Logos Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Logo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Logo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Logo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Logo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Logo|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Logo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Logo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Logo findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LogosTable extends Table
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

        $this->setTable('logos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Josegonzalez/Upload.Upload', [
            'filename' => [
                'pathProcessor' => 'App\Media\PathProcessor',
                'transformer' => 'App\Media\Transformer',
                'path' => 'webroot{DS}img{DS}logos{DS}{user_id}{DS}',
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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('filename', 'create')
            ->notEmpty('filename');

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
                'provider' => 'upload'
            ])
            ->add('filename', 'fileAboveMinWidth', [
                'rule' => ['isAboveMinWidth', 200],
                'message' => 'This image should at least be 200px wide',
                'provider' => 'upload'
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
