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

use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use iboxs\migration\command\factory\Create as FactoryCreate;
use iboxs\migration\command\migrate\Breakpoint as MigrateBreakpoint;
use iboxs\migration\command\migrate\Create as MigrateCreate;
use iboxs\migration\command\migrate\Rollback as MigrateRollback;
use iboxs\migration\command\migrate\Run as MigrateRun;
use iboxs\migration\command\migrate\Status as MigrateStatus;
use iboxs\migration\command\seed\Create as SeedCreate;
use iboxs\migration\command\seed\Run as SeedRun;

class Service extends \iboxs\Service
{

    public function boot()
    {
        $this->app->bind(FakerGenerator::class, function () {
            return FakerFactory::create($this->app->config->get('app.faker_locale', 'zh_CN'));
        });

        $this->app->bind(Factory::class, function () {
            return (new Factory($this->app->make(FakerGenerator::class)))->load($this->app->getRootPath() . 'database/factories/');
        });

        $this->app->bind('migration.creator', Creator::class);

        $this->commands([
            MigrateCreate::class,
            MigrateRun::class,
            MigrateRollback::class,
            MigrateBreakpoint::class,
            MigrateStatus::class,
            SeedCreate::class,
            SeedRun::class,
            FactoryCreate::class,
        ]);
    }
}
