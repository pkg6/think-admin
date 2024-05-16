<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Group extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table("tp_group", ['comment' => '管理组表']);
        $table
            ->addColumn('pid', 'integer', [ 'default' => 0, 'comment' => '父组别'])
            ->addColumn('name', 'string', ['limit' => 50, 'default' => "", 'comment' => '组名'])
            ->addColumn('status', 'integer', ['limit' => 8, 'default' => 1, 'comment' => '状态：0冻结 1正常'])
            ->addColumn('create_time', 'integer', ['null' => false, 'default' => 0, 'timezone' => false,])
            ->addColumn('update_time', 'integer', ['null' => false, 'default' => 0, 'timezone' => false,])
            ->addSoftDelete()
            ->create();

        $table = $this->table("tp_admin_group", ['comment' => '管理员与用户组关联表']);
        $table
            ->addColumn('group_id', 'integer', [ 'default' => 0, 'comment' => '分组ID'])
            ->addColumn('admin_id', 'integer', [ 'default' => 0, 'comment' => '分组ID'])
            ->addSoftDelete()
            ->create();
    }
}
