<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tag Entity.
 *
 * @property int $id
 * @property int $parent_id
 * @property \App\Model\Entity\Tag $parent_tag
 * @property int $lft
 * @property int $rght
 * @property string $name
 * @property bool $listed
 * @property bool $selectable
 * @property \Cake\I18n\Time $created
 * @property \App\Model\Entity\Tag[] $child_tags
 * @property \App\Model\Entity\User[] $users
 */
class Tag extends Entity
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
