<?php
declare( strict_types=1 );

use Phinx\Migration\AbstractMigration;

final class CreateUserTable extends AbstractMigration {
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up(): void {
        $t = $this->table( 'user' );
        $t->addColumn( 'uuid', 'string', [ 'limit' => 40 ] )
            ->addColumn( 'username', 'string', [ 'limit' => 30 ] )
            ->addColumn( 'email', 'string', [ 'limit' => 50 ] )
            ->addColumn( 'password', 'string', [ 'limit' => 255 ] )
            ->addColumn( 'first_name', 'string', [ 'limit' => 30, 'null' => true ] )
            ->addColumn( 'last_name', 'string', [ 'limit' => 30, 'null' => true ] )
            ->addColumn( 'created_at', 'datetime' )
            ->addColumn( 'updated_at', 'datetime', [ 'null' => true ] )
            ->addIndex( 'username', [ 'unique' => true ] )
            ->addIndex( 'email', [ 'unique' => true ] )
            ->addIndex( 'uuid', [ 'unique' => true ] )
            ->create();
    }

    public function down(): void {
        $this->table( 'user' )->drop()->save();
    }
}
