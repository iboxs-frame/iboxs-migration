<?php
// +----------------------------------------------------------------------
// | iboxsPHP [ WE CAN DO IT JUST iboxs IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://iboxsphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace iboxs\migration;

use Phinx\Migration\AbstractMigration;
use iboxs\migration\db\Table;

class Migrator extends AbstractMigration
{
    /**
     * @param string $tableName
     * @param array $options
     * @return Table
     */
    public function table($tableName, $options = []): \Phinx\Db\Table
    {
        return new Table($tableName, $options, $this->getAdapter());
    }
}
