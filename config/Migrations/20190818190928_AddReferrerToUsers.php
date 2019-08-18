<?php
use Migrations\AbstractMigration;

class AddReferrerToUsers extends AbstractMigration
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
        $table->addColumn('referrer', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
            'after' => 'stripe_customer_id'
        ]);
        $table->update();
    }
}
