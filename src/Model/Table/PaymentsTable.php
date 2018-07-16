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

        $this->setTable('payments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('MembershipLevels', [
            'foreignKey' => 'membership_level_id'
        ]);
        $this->belongsTo('AdminAdders', [
            'className' => 'Users',
            'foreignKey' => 'admin_adder_id'
        ]);
        $this->belongsTo('Refunders', [
            'className' => 'Users',
            'foreignKey' => 'refunder_id'
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
            ->add('admin_adder_id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('admin_adder_id', 'create');

        $validator
            ->add('refunder_id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('refunder_id', 'create');

        $validator
            ->add('membership_level_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('membership_level_id', 'create');

        $validator
            ->add('refunded_date', 'valid', ['rule' => 'datetime']);

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
