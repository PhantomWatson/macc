<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
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

        $this->setTable('membership_levels');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Payments', [
            'foreignKey' => 'membership_level_id'
        ]);
        $this->belongsToMany('Users', [
            'foreignKey' => 'membership_level_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'memberships'
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

    /**
     * Prevents a MembershipLevel with associated Memberships from being deleted
     *
     * @param Event $event CakePHP event object
     * @param EntityInterface $entity MembershipLevel entity
     * @param ArrayObject $options Delete operation options
     * @return void
     */
    public function beforeDelete(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $membershipLevelId = $entity->id;
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');
        $hasMemberships = $membershipsTable->exists(['membership_level_id' => $membershipLevelId]);
        if ($hasMemberships) {
            $event->stopPropagation();
        }
    }
}
