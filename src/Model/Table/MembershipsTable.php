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
            ->add('user_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('user_id', 'create');

        $validator
            ->add('membership_level_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('user_id', 'create');

        $validator
            ->add('payment_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('user_id', 'create');

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

        $validator
            ->add('renewed', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('renewed');

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

    /**
     * Finds all memberships that will expire in the next 24 hours, are marked
     * for automatic renewal, and have not been renewed or canceled.
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findToAutoRenew(Query $query, array $options)
    {
        return $query
            ->contain([
                'Users' => function ($q) {
                    return $q->select(['id', 'name', 'email', 'stripe_customer_id']);
                },
                'MembershipLevels' => function ($q) {
                    return $q->select(['id', 'name', 'cost']);
                }
            ])
            ->where(function ($exp, $q) {
                return $exp->isNull('renewed');
            })
            ->where(function ($exp, $q) {
                return $exp->isNull('canceled');
            })
            ->where(function ($exp, $q) {
                return $exp->lte('expires', date('Y-m-d H:i:s', strtotime('+1 day')));
            })
            ->where([
                'recurring_billing' => 1,
            ])
            ->order(['expires' => 'ASC']);
    }

    /**
     * Returns the most recently-purchased membership for the selected user
     *
     * @param int $userId
     * @return array
     */
    public function getCurrentMembership($userId)
    {
        return $this->find('all')
            ->where(['user_id' => $userId])
            ->contain(['MembershipLevels'])
            ->limit(1)
            ->order(['Memberships.created' => 'DESC'])
            ->first();
    }
}
