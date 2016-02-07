<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Payment Entity.
 *
 * @property int $id
 * @property int $user_id
 * @property \App\Model\Entity\User $user
 * @property int $membership_level_id
 * @property \App\Model\Entity\MembershipLevel $membership_level
 * @property string $postback
 * @property int $admin_adder_id
 * @property \App\Model\Entity\AdminAdder $admin_adder
 * @property string $notes
 * @property \Cake\I18n\Time $refunded_date
 * @property int $refunder_id
 * @property \App\Model\Entity\Refunder $refunder
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \App\Model\Entity\MembershipLevelsUser[] $membership_levels_users
 */
class Payment extends Entity
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
