<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MembershipLevelsFixture
 *
 */
class MembershipLevelsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'name' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'cost' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'description' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'name' => 'Artist',
            'cost' => 30,
            'description' => 'Lorem ipsum dolor sit amet',
            'created' => '2016-02-07 00:15:27',
            'modified' => '2016-02-07 00:15:27'
        ],
        [
            'id' => 2,
            'name' => 'Advocate',
            'cost' => 100,
            'description' => 'Lorem ipsum dolor sit amet',
            'created' => '2016-02-07 00:15:27',
            'modified' => '2016-02-07 00:15:27'
        ],
        [
            'id' => 3,
            'name' => 'Ambassador',
            'cost' => 250,
            'description' => 'Lorem ipsum dolor sit amet',
            'created' => '2016-02-07 00:15:27',
            'modified' => '2016-02-07 00:15:27'
        ],
        [
            'id' => 4,
            'name' => 'Arts Hero',
            'cost' => 500,
            'description' => 'Lorem ipsum dolor sit amet',
            'created' => '2016-02-07 00:15:27',
            'modified' => '2016-02-07 00:15:27'
        ],
    ];
}
