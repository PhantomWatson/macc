<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Membership Entity.
 *
 * @property int $id
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property int $membership_level_id
 * @property \App\Model\Entity\MembershipLevel $membership_level
 * @property int $payment_id
 * @property \App\Model\Entity\Payment $payment
 * @property bool $auto_renew
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $expires
 * @property \Cake\I18n\Time $canceled
 */
class Membership extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
