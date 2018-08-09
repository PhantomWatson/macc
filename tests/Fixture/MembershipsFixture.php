<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MembershipsFixture
 *
 */
class MembershipsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'user_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'membership_level_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'payment_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'auto_renew' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'renewed' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'expires' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'canceled' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    private $defaultData = [
        'created' => '2016-02-11 05:09:41',
        'modified' => '2016-02-11 05:09:41',
        'renewed' => null,
        'user_id' => 1,
        'membership_level_id' => 1,
        'payment_id' => 1,
        'auto_renew' => 1,
        'canceled' => null
    ];

    public $records = [];

    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records[] = [
            'id' => 1,
            'expires' => date('Y-m-d H:i:s', strtotime('+6 month')),

        ];
        $this->records[] = [
            'id' => 2,
            'user_id' => 4,
            'expires' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'auto_renew' => 0
        ];
        $this->records[] = [
            'id' => 3,
            'user_id' => 5,
            'expires' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'auto_renew' => 1
        ];

        foreach ($this->records as &$record) {
            $record += $this->defaultData;
        }

        parent::init();
    }
}
