<?php
namespace App\Model\Table;

use App\Model\Entity\Payment;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Payments Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $MembershipLevels
 * @property \Cake\ORM\Association\BelongsTo $AdminAdders
 * @property \Cake\ORM\Association\BelongsTo $Refunders
 * @property \Cake\ORM\Association\HasMany $MembershipLevelsUsers
 */
class PaymentsTable extends Table
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

        $this->table('payments');
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
        $this->belongsTo('AdminAdders', [
            'foreignKey' => 'admin_adder_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Refunders', [
            'foreignKey' => 'refunder_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('MembershipLevelsUsers', [
            'foreignKey' => 'payment_id'
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
            ->requirePresence('postback', 'create')
            ->notEmpty('postback');

        $validator
            ->requirePresence('notes', 'create')
            ->notEmpty('notes');

        $validator
            ->add('refunded_date', 'valid', ['rule' => 'datetime'])
            ->requirePresence('refunded_date', 'create')
            ->notEmpty('refunded_date');

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
        $rules->add($rules->existsIn(['admin_adder_id'], 'AdminAdders'));
        $rules->add($rules->existsIn(['refunder_id'], 'Refunders'));
        return $rules;
    }
}
