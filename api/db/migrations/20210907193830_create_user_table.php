<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserTable extends AbstractMigration
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
        $t = $this->table('user');
        $t->addColumn('username', 'string', ['limit' => 255])
            ->addColumn('email', 'string', ['limit' => 255])
            ->addColumn('password', 'string', ['limit' => 255])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime',['null' => true])
            ->addIndex(['username', 'email'], ['unique' => true])
            ->create();
    }

    public function down(): void
    {
        $this->table('user')->drop()->save();
    }
}
