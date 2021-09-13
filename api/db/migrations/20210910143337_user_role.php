<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UserRole extends AbstractMigration
{
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
    public function up(): void
    {
        $t = $this->table('user_role');
        $t->addColumn('user_id', 'integer')
            ->addColumn('role_id', 'integer', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime',['null' => true])
            ->addIndex(['user_id', 'role_id'], ['unique' => true])
            ->addForeignKey('user_id', 'user', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->addForeignKey('role_id', 'role', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->create();
    }

    public function down(): void
    {
        $this->table('user_role')->drop()->save();
    }
}
