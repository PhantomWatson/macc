<?php
namespace App\Model\Table;

use App\Model\Entity\MembershipLevel;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MembershipLevels Model
 *
 * @property \Cake\ORM\Association\HasMany $Payments
 * @property \Cake\ORM\Association\BelongsToMany $Users
 */
class MembershipLevelsTable extends Table
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

        $this->table('membership_levels');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Payments', [
            'foreignKey' => 'membership_level_id'
        ]);
        $this->belongsToMany('Users', [
            'foreignKey' => 'membership_level_id',
            'targetForeignKey' => 'user_id',
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
            ->notEmpty('name');

        $validator
            ->add('cost', 'valid', ['rule' => 'numeric'])
            ->requirePresence('cost', 'create')
            ->notEmpty('cost');

        return $validator;
    }
}
