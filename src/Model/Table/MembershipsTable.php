<?php
namespace App\Model\Table;

use App\Integrations\LglIntegration;
use App\Model\Entity\Membership;
use App\Model\Entity\User;
use ArrayObject;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * Memberships Model
 *
 * @property BelongsTo $Users
 * @property BelongsTo $MembershipLevels
 * @property BelongsTo $Payments
 */
class MembershipsTable extends Table
{
    const NO_RENEWAL_NEEDED_MSG = 'No memberships need to be renewed at this time.';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('memberships');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

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
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric']);

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
            ->add('auto_renew', 'valid', ['rule' => 'boolean'])
            ->requirePresence('auto_renew', 'create');

        $validator
            ->add('expires', 'valid', ['rule' => 'datetime'])
            ->requirePresence('expires', 'create');

        $validator
            ->add('canceled', 'valid', ['rule' => 'datetime']);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param RulesChecker $rules The rules object to be modified.
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['membership_level_id'], 'MembershipLevels'));
        $rules->add($rules->existsIn(['payment_id'], 'Payments'));
        return $rules;
    }

    /**
     * Finds all memberships that expire today, are marked for auto renewal, and have not been renewed nor canceled
     *
     * @param \Cake\ORM\Query $query
     * @return \Cake\ORM\Query
     */
    public function findToAutoRenew(Query $query)
    {
        return $query
            ->contain([
                'Users' => function (Query $q) {
                    return $q->select(['id', 'name', 'email', 'stripe_customer_id']);
                },
                'MembershipLevels' => function (Query $q) {
                    return $q->select(['id', 'name', 'cost']);
                }
            ])
            ->where([
                function (QueryExpression $exp) {
                    return $exp->isNull('canceled');
                },
                function (QueryExpression $exp) {
                    return $exp->isNull('renewed');
                },
                function (QueryExpression $exp) {
                    return $exp->gte('expires', date('Y-m-d') . ' 00:00:00');
                },
                function (QueryExpression$exp) {
                    return $exp->lte('expires', date('Y-m-d') . ' 23:59:59');
                },
                'auto_renew' => 1,
            ])
            ->orderAsc('expires');
    }

    /**
     * Returns the most recently-purchased membership for the selected user
     *
     * @param int $userId
     * @return Membership|null
     */
    public function getCurrentMembership($userId)
    {
        /** @var Membership $membership */
        $membership = $this->find('all')
            ->where(['user_id' => $userId])
            ->contain(['MembershipLevels'])
            ->limit(1)
            ->order(['Memberships.created' => 'DESC'])
            ->first();

        return $membership;
    }

    /**
     * When a membership is purchased, that user's previous memberships
     * need to have their automatic renewal option turned off. So this does that.
     *
     * @param int $userId
     * @param int $newMembershipId
     */
    public function disablePreviousAutoRenewal($userId, $newMembershipId)
    {
        $memberships = $this->find('all')
            ->where([
                'id !=' => $newMembershipId,
                'user_id' => $userId
            ]);
        foreach ($memberships as $membership) {
            $membership = $this->patchEntity($membership, ['auto_renew' => 0]);
            $this->save($membership);
        }
    }

    /**
     * Checks for a current membership that should be marked as having been renewed by the purchase of a new membership
     *
     * @param int $userId User ID
     * @param int $newMembershipId ID of a membership that has just been purchased
     */
    public function markCurrentMembershipRenewed($userId, $newMembershipId)
    {
        $memberships = $this->find('all')
            ->where([
                'id !=' => $newMembershipId,
                'user_id' => $userId,
                'Memberships.expires >=' => date('Y-m-d H:i:s'),
                function (QueryExpression $exp) {
                    return $exp->isNull('canceled');
                },
                function (QueryExpression $exp) {
                    return $exp->isNull('renewed');
                }
            ]);
        foreach ($memberships as $membership) {
            $membership = $this->patchEntity($membership, ['renewed' => new Time()]);
            $this->save($membership);
        }
    }

    /**
     * Returns a string warning the user about when their membership will/did expire, or null if not applicable
     *
     * @param int $userId User ID
     * @return null|string
     */
    public function getMembershipExpirationWarning($userId)
    {
        if (!$userId) {
            return null;
        }

        /** @var Membership $membership */
        $membership = $this->find('all')
            ->select(['id', 'expires'])
            ->where([
                'Memberships.user_id' => $userId,
                function (QueryExpression $exp) {
                    return $exp->isNull('canceled');
                }
            ])
            ->contain([
                'MembershipLevels' => function (Query $q) {
                    return $q->select(['id', 'name']);
                }
            ])
            ->order(['Memberships.created' => 'DESC'])
            ->first();

        if (!$membership) {
            return null;
        }

        $hasExpired = $membership->expires->format('Y-m-d H:i:s') < date('Y-m-d H:i:s');
        $msg = $hasExpired ? 'Your "%s" membership expired on %s' : 'Your "%s" membership will expire on %s';

        return sprintf(
            $msg,
            $membership->membership_level->name,
            $membership->expires->format('F jS, Y')
        );
    }

    /**
     * Returns a simple array of User IDs who have expired memberships but no current memberships
     *
     * @return array
     */
    public function getUserIdsWithUnrenewedMemberships()
    {
        $usersWithExpiredMemberships = $this->find()
            ->select(['user_id'])
            ->where([
                function (QueryExpression $exp) {
                    return $exp->lte('expires', date('Y-m-d H:i:s'));
                }
            ])
            ->distinct(['user_id'])
            ->enableHydration(false)
            ->toArray();
        $usersWithExpiredMemberships = Hash::extract($usersWithExpiredMemberships, '{n}.user_id');

        $usersWithCurrentMemberships = $this->find()
            ->select(['user_id'])
            ->where([
                function (QueryExpression $exp) {
                    return $exp
                        ->gte('expires', date('Y-m-d H:i:s'))
                        ->isNull('canceled')
                        ->isNull('renewed');
                },
            ])
            ->matching('Payments', function (Query $query) {
                return $query->where(function (QueryExpression $exp) {
                    return $exp->isNull('refunded_date');
                });
            })
            ->distinct(['Memberships.user_id'])
            ->enableHydration(false)
            ->toArray();
        $usersWithCurrentMemberships = Hash::extract($usersWithCurrentMemberships, '{n}.user_id');

        return array_filter(
            $usersWithExpiredMemberships,
            function ($userId) use ($usersWithCurrentMemberships) {
                return !in_array($userId, $usersWithCurrentMemberships);
            }
        );
    }

    /**
     * afterSave callback method
     *
     * @param Event $event CakePHP event
     * @param EntityInterface $membership Membership entity
     * @param ArrayObject $options Options array
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $membership, ArrayObject $options)
    {
        /** @var Membership $membership */
        $this->updateLglIntegration($membership);
    }

    /**
     * Sends constituent info updates to LGL
     *
     * @param Membership $membership Membership entity
     * @return void
     */
    private function updateLglIntegration(Membership $membership)
    {
        if ($membership->isNew()) {
            /** @var User $user */
            $user = TableRegistry::getTableLocator()
                ->get('Users')
                ->get($membership->user_id);
            (new LglIntegration())->addMembership($user, $membership);
        }
    }
}
