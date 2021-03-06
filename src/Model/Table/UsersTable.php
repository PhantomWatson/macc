<?php
namespace App\Model\Table;

use App\Integrations\LglIntegration;
use App\MailingList\MailingList;
use App\Model\Entity\Membership;
use App\Model\Entity\User;
use ArrayObject;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Exception;
use Stripe\Customer;
use Stripe\Stripe;

/**
 * Users Model
 *
 * @property HasMany $Payments
 * @property BelongsToMany $MembershipLevels
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
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric']);

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
            ->allowEmptyString('email', false);

        $validator
            ->requirePresence('password', 'create')
            ->add('password', 'notBlank', [
                'rule' => 'notBlank',
                'message' => 'A non-blank password is required.'
            ]);

        $validator
            ->requirePresence('role', 'create')
            ->allowEmptyString('role', false)
            ->add('role', 'valid', [
                'rule' => function ($data) {
                    if (in_array($data, ['admin', 'user'])) {
                        return true;
                    }
                    return 'Role must be admin or user.';
                }
            ]);

        $validator
            ->allowEmptyString('new_password', 'update', 'A password is required')
            ->add('new_password', 'validNewPassword1', [
                'rule' => ['compareWith', 'confirm_password'],
                'message' => 'Sorry, those passwords did not match.'
            ]);

        $validator
            ->allowEmptyString('confirm_password', 'update', 'A password is required');

        $validator
            ->maxLength('address', 255)
            ->allowEmptyString('address');

        $validator
            ->maxLength('city', 50)
            ->allowEmptyString('city');

        $validator
            ->maxLength('state', 2)
            ->allowEmptyString('state');

        $validator
            ->maxLength('zipcode', 15)
            ->allowEmptyString('zipcode');

        $validator
            ->allowEmptyString('current_password')
            ->add('current_password', 'custom', [
                'rule' =>
                    function ($value, $context) {
                        /** @var User $user */
                        $user = $this->find()
                            ->select(['password'])
                            ->where(['id' => $context['data']['id']])
                            ->first();

                        return (new DefaultPasswordHasher)->check($value, $user->password);
                    },
                'message' => 'Current password is incorrect'
            ]);

        $validator
            ->maxLength('referrer', 255)
            ->allowEmptyString('referrer');

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
     * Finds users with non-expired, non-canceled memberships that are associated with non-refunded payments
     *
     * @param Query $query
     * @return Query
     */
    public function findMembers(Query $query)
    {
        return $query->matching('Memberships', function ($q) {
            /** @var Query $q */

            return $q
                ->where(['Memberships.expires >=' => date('Y-m-d H:i:s')])
                ->where([
                    function (QueryExpression $exp) {
                        return $exp->isNull('canceled');
                    }
                ])
                ->matching('Payments', function (Query $query) {
                    return $query->where(function (QueryExpression $exp) {
                        return $exp->isNull('refunded_date');
                    });
                });
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
     * Returns true if the user has a non-expired, non-canceled membership associated with a non-refunded payment
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
            ->matching('Payments', function (Query $query) {
                return $query->where(function (QueryExpression $exp) {
                    return $exp->isNull('refunded_date');
                });
            })
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
        if ($this->isCurrentMember($userId)) {
            return false;
        }
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
        /** @var MembershipsTable $membershipsTable */
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
                        ])
                        ->orderDesc('expires');
                }
            ]);
    }

    /**
     * afterSave callback method
     *
     * @param Event $event CakePHP event
     * @param EntityInterface $user User entity
     * @param ArrayObject $options Options array
     * @return void
     * @throws Exception
     */
    public function afterSave(Event $event, EntityInterface $user, ArrayObject $options)
    {
        /** @var User $user */
        $this->updateLglIntegration($user);
        $this->updateMailChimp($user);
    }

    /**
     * Sends constituent info updates to LGL
     *
     * @param User $user User entity
     * @return void
     */
    private function updateLglIntegration(User $user)
    {
        // Add new constituent to LGL
        if ($user->isNew()) {
            (new LglIntegration())->addUser($user);

            return;
        }

        // Update constituent name in LGL
        $nameUpdated = $user->name != $user->getOriginal('name');
        if ($nameUpdated) {
            (new LglIntegration())->updateName($user);

            return;
        }

        // Update constituent contact info in LGL
        $contactFields = [
            'email',
            'address',
            'city',
            'state',
            'zipcode'
        ];
        foreach ($contactFields as $contactField) {
            $hasChanged = $user->$contactField != $user->getOriginal($contactField);
            if ($hasChanged) {
                (new LglIntegration())->updateContact($user);

                return;
            }
        }
    }

    /**
     * Updates a user's MailChimp subscription information
     *
     * @param User $user User entity
     * @throws Exception
     */
    private function updateMailChimp($user)
    {
        $oldEmail = $user->getOriginal('email');
        $newEmail = $user->email;
        $emailChanged = $newEmail != $oldEmail;

        // Update email address in MailChimp
        if ($emailChanged && MailingList::isMember($oldEmail)) {
            MailingList::updateEmailAddress($oldEmail, $newEmail);
        }
    }

    /**
     * Returns a boolean indicating if the specified user qualifies for having their logo displayed
     *
     * @param int $userId User ID
     * @return bool
     */
    public static function qualifiesForLogo($userId)
    {
        if (!$userId) {
            return false;
        }

        /** @var Membership $membership */
        $membership = TableRegistry::getTableLocator()
            ->get('Memberships')
            ->getCurrentMembership($userId);

        if (!$membership) {
            return false;
        }

        return Membership::qualifiesForLogo($membership->membership_level_id);
    }
}
