<?php
// +----------------------------------------------------------------------
// | iboxs [ WE CAN DO IT JUST iboxs IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.iboxs.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhangyajun <448901948@qq.com>
// +----------------------------------------------------------------------

namespace iboxs\migration\command\migrate;

use InvalidArgumentException;
use RuntimeException;
use iboxs\console\Command;
use iboxs\console\Input;
use iboxs\console\input\Argument as InputArgument;
use iboxs\console\Output;
use iboxs\migration\Creator;

class Create extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('migrate:create')
            ->setDescription('Create a new migration')
            ->addArgument('name', InputArgument::REQUIRED, 'What is the name of the migration?')
            ->setHelp(sprintf('%sCreates a new database migration%s', PHP_EOL, PHP_EOL));
    }

    /**
     * Create the new migration.
     *
     * @param Input  $input
     * @param Output $output
     * @return void
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    protected function execute(Input $input, Output $output)
    {
        /** @var Creator $creator */
        $creator = $this->app->get('migration.creator');

        $className = $input->getArgument('name');

        $path = $creator->create($className);

        $output->writeln('<info>created</info> .' . str_replace(getcwd(), '', realpath($path)));
    }

}
