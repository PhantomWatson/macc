<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zipcode
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \App\Model\Entity\Payment[] $payments
 * @property \App\Model\Entity\MembershipLevel[] $membership_levels
 */
class User extends Entity
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

    /**
     * Fields that are excluded from JSON an array versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];

    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }

    protected function _getMainPictureFullsize()
    {
        if (! $this->_properties['main_picture_id']) {
            return null;
        }
        foreach ($this->_properties['pictures'] as $picture) {
            if ($picture['id'] == $this->_properties['main_picture_id']) {
                return $picture['filename'];
            }
        }
        return null;
    }

    protected function _getMainPictureThumb()
    {
        if (! $this->_properties['main_picture_id']) {
            return null;
        }
        foreach ($this->_properties['pictures'] as $picture) {
            if ($picture['id'] == $this->_properties['main_picture_id']) {
                return $picture['thumbnail_filename'];
            }
        }
        return null;
    }
}
