<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MembershipRenewalLog Model
 *
 */
class MembershipRenewalLogsTable extends Table
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

        $this->setTable('membership_renewal_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->integer('id');

        $validator
            ->requirePresence('message', 'create');

        $validator
            ->boolean('error')
            ->requirePresence('error', 'create');

        return $validator;
    }

    /**
     * Adds a log entry
     *
     * @param string $message Message
     * @param bool $error Error flag
     * @return bool
     */
    public function logAutoRenewal($message, $error = false)
    {
        $logEntry = $this->newEntity([
            'message' => $message,
            'error' => $error
        ]);
        return (boolean)$this->save($logEntry);
    }
}
