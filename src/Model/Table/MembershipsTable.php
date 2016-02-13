<?php
namespace App\Model\Table;

use App\Model\Entity\Membership;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Memberships Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $MembershipLevels
 * @property \Cake\ORM\Association\BelongsTo $Payments
 */
class MembershipsTable extends Table
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

        $this->table('memberships');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('MembershipLevels', [
            'foreignKey' => 'membership_level_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Payments', [
            'foreignKey' => 'payment_id',
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
            ->add('recurring_billing', 'valid', ['rule' => 'boolean'])
            ->requirePresence('recurring_billing', 'create')
            ->notEmpty('recurring_billing');

        $validator
            ->add('expires', 'valid', ['rule' => 'datetime'])
            ->requirePresence('expires', 'create')
            ->notEmpty('expires');

        $validator
            ->add('canceled', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('canceled');

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
        $rules->add($rules->existsIn(['membership_level_id'], 'MembershipLevels'));
        $rules->add($rules->existsIn(['payment_id'], 'Payments'));
        return $rules;
    }
}