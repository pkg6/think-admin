<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Admin extends Migrator
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
        $table = $this->table("tp_admin", ['comment' => '管理员表']);
        $table->addColumn('username', 'string', ['limit' => 50, 'default' => '', 'comment' => '用户名，登陆使用'])
            ->addColumn('nickname', 'string', ['limit' => 50, 'default' => '', 'comment' => '昵称'])
            ->addColumn('email', 'string', ['limit' => 255, 'default' => '', 'comment' => '邮件'])
            ->addColumn('mobile', 'string', ['limit' => 18, 'default' => '', 'comment' => '手机号'])
            ->addColumn('password', 'string', ['limit' => 255, 'default' => '', 'comment' => '用户密码'])
            ->addColumn('avatar', 'string', ['limit' => 255, 'default' => '', 'comment' => '头像'])
            ->addColumn('loginfailure', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '失败次数'])
            ->addColumn('logintime', 'integer', ['limit' => 32, 'default' => 0, 'comment' => '登录时间'])
            ->addColumn('loginip', 'string', ['limit' => 11, 'default' => "", 'comment' => '最后登录IP'])
            ->addColumn('session_id', 'string', ['limit' => 255, 'default' => "", 'comment' => 'Session标识'])
            ->addColumn('remember_token', 'string', ['limit' => 255, 'default' => "", 'comment' => '记住token标识'])
            ->addColumn('status', 'integer', ['limit' => 8, 'default' => 1, 'comment' => '状态：0冻结 1正常'])
            ->addColumn('create_time', 'integer', ['null' => false, 'default' => 0, 'timezone' => false,])
            ->addColumn('update_time', 'integer', ['null' => false, 'default' => 0, 'timezone' => false,])
            ->addSoftDelete()
            ->addIndex(['username'], ['unique' => true])
            ->create();
    }
}
