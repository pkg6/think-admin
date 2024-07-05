<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Rule extends Migrator
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
        $table = $this->table("tp_rule", ['comment' => '节点表']);
        $table
            ->addColumn('pid', 'integer', ['default' => 0, 'comment' => '父ID'])
            ->addColumn('name', 'string', ['limit' => 50, 'default' => "", 'comment' => '节点名称'])
            ->addColumn('title', 'string', ['limit' => 50, 'default' => "", 'comment' => '节点名称'])
            ->addColumn('icon', 'string', ['limit' => 255, 'default' => "", 'comment' => '图标'])
            ->addColumn('url', 'string', ['limit' => 255, 'default' => "", 'comment' => '规则URL'])
            ->addColumn('condition', 'string', ['limit' => 255, 'default' => "", 'comment' => '规则URL条件'])
            ->addColumn('remark', 'string', ['limit' => 255, 'default' => "", 'comment' => '备注'])
            ->addColumn('type', 'integer', ['limit' => 8, 'default' => 1, 'comment' => '1菜单 2权限节点'])
            ->addColumn('status', 'integer', ['limit' => 8, 'default' => 1, 'comment' => '状态：0冻结 1正常'])
            ->addColumn('create_time', 'integer', ['null' => false, 'default' => 0, 'timezone' => false,])
            ->addColumn('update_time', 'integer', ['null' => false, 'default' => 0, 'timezone' => false,])
            ->addSoftDelete()
            ->create();

        $table = $this->table("tp_group_rule", ['comment' => '用户组与节点关联表']);
        $table
            ->addColumn('group_id', 'integer', ['default' => 0, 'comment' => '分组ID'])
            ->addColumn('rule_id', 'integer', ['default' => 0, 'comment' => '节点ID'])
            ->addSoftDelete()
            ->create();

        $table = $this->table("tp_admin_rule", ['comment' => '管理员与节点关联表']);
        $table
            ->addColumn('admin_id', 'integer', ['default' => 0, 'comment' => '分组ID'])
            ->addColumn('rule_id', 'integer', ['default' => 0, 'comment' => '节点ID'])
            ->addSoftDelete()
            ->create();
    }
}
