<?php

namespace LaraAreaMake\Console\Abstracts;

abstract class BladeMaker extends PhpMaker
{
    /**
     * @var string
     */
    public $extension = '.blade.php';

    /**
     * @var string
     */
    public $stub = 'general' . DIRECTORY_SEPARATOR . 'view.stub';

    /**
     * @var string
     */
    public $rootPath = 'resources' . DIRECTORY_SEPARATOR . 'views';
}
