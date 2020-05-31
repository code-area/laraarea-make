<?php

namespace LaraAreaMake\Console\Abstracts;

use LaraAreaMake\Console\Traits\Keywords\NamespaceKeywordTrait;
use LaraAreaMake\Console\Traits\Keywords\UseKeywordTrait;

abstract class PhpMaker extends BaseMaker
{
    use NamespaceKeywordTrait, UseKeywordTrait;

    /**
     * php artisan area-make:php test
     * php artisan area-make:php test,test1,test2
     * php artisan area-make:php test --path=vvv
     * php artisan area-make:php test --root-path=vvv
     * php artisan area-make:php test --path=vvv --root-path=vvv
     * php artisan area-make:php test --path=Www --root-path=vvv
     *
     * php artisan area-make:php test --namespace=TestNamespace
     * php artisan area-make:php test --use=TestNamespace
     * php artisan area-make:php test --use=use3/usesame,use4/usesame,use5/use4/usesame
     * php artisan area-make:php test --content="\$a = 1;PHP_EOL\$b = 2;PHP_EOLecho \$a + \$b;//3"
     * php artisan area-make:php test --content="\$variable = 'value';"
     *
     * php artisan area-make:php test --namespace=TestNamespace --use=TestNamespace
     * php artisan area-make:php test --namespace=TestNamespace --content="\$variable = 'value';"
     * php artisan area-make:php test --use=TestNamespace --content="\$variable = 'value';"
     *
     * php artisan area-make:php test --namespace=TestNamespace --use=TestNamespace --content="\$variable = 'value';"
     */

    /**
     * @var string
     */
    public $extension = '.php';

    /**
     * @var string
     */
    public $stub = 'general' . DIRECTORY_SEPARATOR . 'php.stub';

    /**
     * @var array
     */
    public $keywords = [
        'namespace',
        'use',
        'content',
    ];

    /**
     * Used for set signature with arguments, options
     *  [
     *      option => option, // used for options
     *      option            // used for options with value
     *  ]
     * @var array
     */
    protected $defaultOptions = [
        'confirm' => 'confirm : Confirm all system correction inputs',
        'confirm-default' => 'confirm-default= : Set [false, no or 0] for not permit any changes make by system',
        'confirm-backslash' => 'confirm-backslash : Confirm correct backslash. Must be replace \ to /',
        'confirm-overwrite' => 'confirm-overwrite : Confirm overwrite existing files',
        'choice-default' => 'choice-default : Set default choice',
        'path' => 'path= : Path where must be generate file, also based this must be make namespace',
        'root-path' => 'root-path= : Root Path where must be generate file without using in namespace',
    ];

    /**
     * All parts of input must be process by methods
     *
     * keyword => method
     * keyword => [methods,...]
     *
     * @TODO, validate
     * @var array
     */
    public $defaultKeywordBackSlashParts = [
        'pattern' => [
            '\Illuminate\Support\Str::camel',
            'ucfirst',
        ],
        'use' => [
            '\Illuminate\Support\Str::camel',
            'ucfirst',
        ],
        'namespace' => [
            '\Illuminate\Support\Str::camel',
            'ucfirst',
        ],
    ];

    /**
     * @var array
     */
    public $guessKeywords = [
        'namespace'
    ];

    /**
     * Corrected backslash keywords / to \
     *
     * @var array
     */
    public $backslashKeywords = [
        'namespace',
        'use',
    ];

    /**
     * Root path not included in namespace
     *
     * @var string
     */
    public $rootPath = '';

    /**
     * Root path not included in namespace
     *
     * @var
     */
    protected $__rootPath;

    /**
     * @var
     */
    public $__content;

    /**
     * @param string $path
     * @return string
     */
    public function getRelativePath($path = '')
    {
        $this->rootPath = $this->processPrePath($this->__rootPath, $this->rootPath);
        $this->rootPath = $this->processRelativePath($this->rootPath);
        return parent::getRelativePath($this->rootPath);
    }

    /**
     * @param $content
     * @param $keyword
     * @param $input
     * @return mixed
     */
    public function replaceContentKeyword($content, $keyword, $input)
    {
        if (empty($input)) {
            return $this->replaceContent(PHP_EOL . $keyword . PHP_EOL, '', $content);
        }

        $input = $this->processSpecialSymbols($input, 'PHP_EOL', PHP_EOL);
        return $this->replaceContent($keyword, $input, $content);
    }

    /**
     * TODO
     *
     * @param $content
     * @return mixed
     */
    public function trimFinalContent($content)
    {
        return $this->replaceContent(PHP_EOL . PHP_EOL . PHP_EOL . 'class', PHP_EOL . PHP_EOL . 'class', $content);
    }
}
