<?php
use Migrations\AbstractMigration;

class AddAddressFieldsToUsers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('address', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
            'after' => 'profile'
        ]);
        $table->addColumn('city', 'string', [
            'default' => null,
            'limit' => 50,
            'null' => false,
            'after' => 'address'
        ]);
        $table->addColumn('state', 'string', [
            'default' => null,
            'limit' => 2,
            'null' => false,
            'after' => 'city'
        ]);
        $table->addColumn('zipcode', 'string', [
            'default' => null,
            'limit' => 15,
            'null' => false,
            'after' => 'state'
        ]);
        $table->update();
    }
}
