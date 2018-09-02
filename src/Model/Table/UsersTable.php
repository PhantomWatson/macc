<?php
namespace App\Model\Table;

use App\Model\Entity\Membership;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Stripe\Customer;
use Stripe\Stripe;

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

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Xety/Cake3Sluggable.Sluggable', [
            'field' => 'name'
        ]);

        $this->hasMany('Payments', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Memberships', [
            'dependent' => true,
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Pictures', [
            'dependent' => true,
            'foreignKey' => 'user_id'
        ]);
        $this->belongsToMany('Tags', [
            'dependent' => true,
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'tag_id',
            'joinTable' => 'tags_users'
        ]);
        $this->hasOne('Logos', [
            'foreignKey' => 'user_id'
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
                'rule' => function ($data) {
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

        $validator
            ->maxLength('address', 255)
            ->allowEmpty('address');

        $validator
            ->maxLength('city', 50)
            ->allowEmpty('city');

        $validator
            ->maxLength('state', 2)
            ->allowEmpty('state');

        $validator
            ->maxLength('zipcode', 15)
            ->allowEmpty('zipcode');

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

    /**
     * Finds all users with memberships that have not expired or been canceled
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findMembers(Query $query)
    {
        return $query->matching('Memberships', function ($q) {
            /** @var Query $q */

            return $q->where(['Memberships.expires >=' => date('Y-m-d H:i:s')])->where([
                function (QueryExpression $exp) {
                    return $exp->isNull('canceled');
                }
            ]);
        })->distinct(['Users.id']);
    }

    /**
     * Creates a Stripe customer and returns the customer object
     *
     * @param int $userId
     * @param array $token
     * @return Customer
     */
    public function createStripeCustomer($userId, $token)
    {
        $apiKey = Configure::read('Stripe.Secret');
        Stripe::setApiKey($apiKey);

        $user = $this->get($userId);

        return Customer::create([
            'source' => $token,
            'description' => $user->name,
            'email' => $user->email,
            'metadata' => [
                'macc_user_id' => $user->id
            ]
        ]);
    }

    /**
     * Returns true if the user has a non-expired, non-canceled membership
     *
     * @param int $userId
     * @return boolean
     */
    public function isCurrentMember($userId)
    {
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');
        $count = $membershipsTable->find('all')
            ->where([
                'Memberships.user_id' => $userId,
                'Memberships.expires >=' => date('Y-m-d H:i:s'),
                function (QueryExpression $exp) {
                    return $exp->isNull('canceled');
                }
            ])
            ->count();
        return $count > 0;
    }

    /**
     * Returns true if the user has no current membership, but has a membership that expired without being canceled
     *
     * @param int $userId
     * @return bool
     */
    public function hasExpiredMembership($userId)
    {
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');
        $count = $membershipsTable->find('all')
            ->where([
                'Memberships.user_id' => $userId,
                'Memberships.expires <' => date('Y-m-d H:i:s'),
                function (QueryExpression $exp) {
                    return $exp->isNull('canceled');
                }
            ])
            ->count();
        return $count > 0;
    }

    /**
     * Returns true if the user has any previous membership records, regardless of current/expired status
     *
     * @param int $userId
     * @return bool
     */
    public function hasMembership($userId)
    {
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');
        $count = $membershipsTable->find('all')
            ->where(['Memberships.user_id' => $userId])
            ->count();

        return $count > 0;
    }

    /**
     * Finds all users that qualify to have their logos displayed in the footer,
     * intended to be chained after find('members')
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findQualifiedForLogo(Query $query)
    {
        return $query->matching('Memberships', function (Query $q) {
            return $q->where([
                function (QueryExpression $exp) {
                    return $exp->in('membership_level_id', Membership::getLogoQualifyingLevels());
                }
            ]);
        });
    }

    /**
     * Finds users who have expired memberships but no current memberships
     *
     * @param Query $query
     * @return array|Query
     */
    public function findWithUnrenewedMemberships(Query $query)
    {
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');
        $userIds = $membershipsTable->getUserIdsWithUnrenewedMemberships();

        return $query
            ->where([
                function (QueryExpression $exp) use ($userIds) {
                    return $exp->in('id', $userIds);
                }
            ])
            ->contain([
                'Memberships' => function (Query $q) {
                    return $q
                        ->select([
                            'expires',
                            'membership_level_id',
                            'user_id'
                        ])
                        ->contain([
                            'MembershipLevels' => function (Query $q) {
                                return $q
                                    ->select(['name'])
                                    ->orderDesc('created');
                            }
                        ]);
                }
            ]);
    }
}
