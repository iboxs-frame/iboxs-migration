<?php
// +----------------------------------------------------------------------
// | iboxs [ WE CAN DO IT JUST iboxs IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.iboxs.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhangyajun <448901948@qq.com>
// +----------------------------------------------------------------------

namespace iboxs\migration\command\migrate;

use Phinx\Migration\MigrationInterface;
use iboxs\console\Input;
use iboxs\console\input\Option as InputOption;
use iboxs\console\Output;
use iboxs\migration\command\Migrate;

class Run extends Migrate
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('migrate:run')
            ->setDescription('Migrate the database')
            ->addOption('--target', '-t', InputOption::VALUE_REQUIRED, 'The version number to migrate to')
            ->addOption('--date', '-d', InputOption::VALUE_REQUIRED, 'The date to migrate to')
            ->setHelp(<<<EOT
The <info>migrate:run</info> command runs all available migrations, optionally up to a specific version

<info>php iboxs migrate:run</info>
<info>php iboxs migrate:run -t 20110103081132</info>
<info>php iboxs migrate:run -d 20110103</info>
<info>php iboxs migrate:run -v</info>

EOT
            );
    }

    /**
     * Migrate the database.
     *
     * @param Input  $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        $version = $input->getOption('target');
        $date    = $input->getOption('date');

        // run the migrations
        $start = microtime(true);
        if (null !== $date) {
            $this->migrateToDateTime(new \DateTime($date));
        } else {
            $this->migrate($version);
        }
        $end = microtime(true);

        $output->writeln('');
        $output->writeln('<comment>All Done. Took ' . sprintf('%.4fs', $end - $start) . '</comment>');
    }

    public function migrateToDateTime(\DateTime $dateTime)
    {
        $versions   = array_keys($this->getMigrations());
        $dateString = $dateTime->format('YmdHis');

        $outstandingMigrations = array_filter($versions, function ($version) use ($dateString) {
            return $version <= $dateString;
        });

        if (count($outstandingMigrations) > 0) {
            $migration = max($outstandingMigrations);
            $this->output->writeln('Migrating to version ' . $migration);
            $this->migrate($migration);
        }
    }

    protected function migrate($version = null)
    {
        $migrations = $this->getMigrations();
        $versions   = $this->getVersions();
        $current    = $this->getCurrentVersion();

        if (empty($versions) && empty($migrations)) {
            return;
        }

        if (null === $version) {
            $version = max(array_merge($versions, array_keys($migrations)));
        } else {
            if (0 != $version && !isset($migrations[$version])) {
                $this->output->writeln(sprintf('<comment>warning</comment> %s is not a valid version', $version));
                return;
            }
        }

        // are we migrating up or down?
        $direction = $version > $current ? MigrationInterface::UP : MigrationInterface::DOWN;

        if ($direction === MigrationInterface::DOWN) {
            // run downs first
            krsort($migrations);
            foreach ($migrations as $migration) {
                if ($migration->getVersion() <= $version) {
                    break;
                }

                if (in_array($migration->getVersion(), $versions)) {
                    $this->executeMigration($migration, MigrationInterface::DOWN);
                }
            }
        }

        ksort($migrations);
        foreach ($migrations as $migration) {
            if ($migration->getVersion() > $version) {
                break;
            }

            if (!in_array($migration->getVersion(), $versions)) {
                $this->executeMigration($migration, MigrationInterface::UP);
            }
        }
    }

    protected function getCurrentVersion()
    {
        $versions = $this->getVersions();
        $version  = 0;

        if (!empty($versions)) {
            $version = end($versions);
        }

        return $version;
    }
}
