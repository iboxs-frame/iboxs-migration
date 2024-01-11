<?php

namespace iboxs\migration;

use iboxs\console\Output;

class NullOutput extends Output
{
    public function __construct()
    {
        parent::__construct('nothing');
    }
}
