<?php

use Jmleroux\Command\GsParseCommand;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * @author JM Leroux <jmleroux.pro@gmail.com>
 */
class Application extends BaseApplication
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'GS-parser')
    {
        parent::__construct($name);

        $this->add(new GsParseCommand(__DIR__ . '/var'));
    }
}
