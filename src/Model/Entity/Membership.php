<?php
namespace App\Model\Entity;

use Cake\I18n\Time;
use Cake\ORM\Entity;

/**
 * Membership Entity.
 *
 * @property int $id
 * @property int $user_id
 * @property User $user
 * @property int $membership_level_id
 * @property MembershipLevel $membership_level
 * @property int $payment_id
 * @property Payment $payment
 * @property bool $auto_renew
 * @property Time $created
 * @property Time $modified
 * @property Time $expires
 * @property Time $canceled
 */
class Membership extends Entity
{
    const AMBASSADOR_LEVEL = 3;
    const ARTS_HERO_LEVEL = 4;

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

    /**
     * Returns TRUE if the provided membership level qualifies for a footer logo
     *
     * @param int $membershipLevelId ID for a membership level record
     * @return bool
     */
    public static function qualifiesForLogo($membershipLevelId)
    {
        return in_array($membershipLevelId, self::getLogoQualifyingLevels());
    }

    /**
     * Returns the membership levels that qualify for logos in the footer, in the same order they should be displayed
     *
     * @return array
     */
    public static function getLogoQualifyingLevels()
    {
        return [
            self::ARTS_HERO_LEVEL,
            self::AMBASSADOR_LEVEL
        ];
    }
}
