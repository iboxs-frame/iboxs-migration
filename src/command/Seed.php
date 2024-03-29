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

namespace iboxs\migration\command;

use InvalidArgumentException;
use Phinx\Seed\AbstractSeed;
use Phinx\Util\Util;
use iboxs\migration\Command;
use iboxs\migration\Seeder;

abstract class Seed extends Command
{

    /**
     * @var array
     */
    protected $seeds;

    protected function getPath()
    {
        return $this->app->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'seeds';
    }

    public function getSeeds()
    {
        if (null === $this->seeds) {
            $phpFiles = glob($this->getPath() . DIRECTORY_SEPARATOR . '*.php', defined('GLOB_BRACE') ? GLOB_BRACE : 0);

            // filter the files to only get the ones that match our naming scheme
            $fileNames = [];
            /** @var Seeder[] $seeds */
            $seeds = [];

            foreach ($phpFiles as $filePath) {
                if (Util::isValidSeedFileName(basename($filePath))) {
                    // convert the filename to a class name
                    $class             = pathinfo($filePath, PATHINFO_FILENAME);
                    $fileNames[$class] = basename($filePath);

                    // load the seed file
                    /** @noinspection PhpIncludeInspection */
                    require_once $filePath;
                    if (!class_exists($class)) {
                        throw new InvalidArgumentException(sprintf('Could not find class "%s" in file "%s"', $class, $filePath));
                    }

                    // instantiate it
                    $seed = new $class($this->input, $this->output);

                    if (!($seed instanceof AbstractSeed)) {
                        throw new InvalidArgumentException(sprintf('The class "%s" in file "%s" must extend \Phinx\Seed\AbstractSeed', $class, $filePath));
                    }

                    $seeds[$class] = $seed;
                }
            }

            ksort($seeds);
            $this->seeds = $seeds;
        }

        return $this->seeds;
    }
}
