<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $Payments
 * @property \Cake\ORM\Association\BelongsToMany $MembershipLevels
 */
class UsersTable extends Table
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

        $this->table('users');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Payments', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsToMany('MembershipLevels', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'membership_level_id',
            'joinTable' => 'membership_levels_users'
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
            ->requirePresence('name', 'create')
            ->add('name', 'notBlank', [
                'rule' => 'notBlank',
                'message' => 'A non-blank name is required.'
            ]);

        $validator
            ->add('email', 'valid', [
                'rule' => 'email',
                'message' => 'That doesn\'t appear to be a valid email address.'
            ])
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Sorry, another account has already been created with that email address.'
            ])
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->requirePresence('password', 'create')
            ->add('password', 'notBlank', [
                'rule' => 'notBlank',
                'message' => 'A non-blank password is required.'
            ]);

        $validator
            ->requirePresence('role', 'create')
            ->notEmpty('role')
            ->add('role', 'valid', [
                'rule' => function ($data, $provider) {
                    if (in_array($data, ['admin', 'user'])) {
                        return true;
                    }
                    return 'Role must be admin or user.';
                }
            ]);

        $validator
            ->notEmpty('new_password', 'A password is required', 'create')
            ->allowEmpty('new_password', 'update')
            ->add('new_password', 'validNewPassword1', [
                'rule' => ['compareWith', 'confirm_password'],
                'message' => 'Sorry, those passwords did not match.'
            ]);

        $validator
            ->notEmpty('confirm_password', 'A password is required', 'create')
            ->allowEmpty('confirm_password', 'update');

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
        $rules->add($rules->isUnique(['email']));
        return $rules;
    }

    /**
     * @param string $email
     * @return int|null
     */
    public function getIdWithEmail($email)
    {
        $user = $this->find('all')
            ->select(['id'])
            ->where(['email' => $email])
            ->limit(1);
        if ($user->isEmpty()) {
            return null;
        }
        return $user->first()->id;
    }
}
