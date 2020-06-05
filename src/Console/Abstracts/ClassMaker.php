<?php

namespace LaraAreaMake\Console\Abstracts;

use LaraAreaMake\Console\Traits\Keywords\AbstractKeywordTrait;
use LaraAreaMake\Console\Traits\Keywords\InterfaceKeywordTrait;
use LaraAreaMake\Console\Traits\Keywords\TraitKeywordTrait;
use LaraAreaMake\Console\Traits\Keywords\ConstantKeywordTrait;
use LaraAreaMake\Console\Traits\Keywords\PropertyKeywordTrait;
use LaraAreaMake\Console\Traits\TemplatesTrait;

abstract class ClassMaker extends ExtendablePhpMaker
{
    use
        InterfaceKeywordTrait,
        TraitKeywordTrait,
        ConstantKeywordTrait,
        PropertyKeywordTrait,
        AbstractKeywordTrait,
        TemplatesTrait;

    /**
     * php artisan area-make:class Test1 --path=vvv --confirm-overwrite
     *
     * php artisan area-make:class Test2 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass"
     * php artisan area-make:class Test3 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];"
     * php artisan area-make:class Test4 --path=vvv --confirm-overwrite --property="protected \$test = 'test';"
     * php artisan area-make:class Test5 --path=vvv --confirm-overwrite --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     *
     * php artisan area-make:class Test6 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];"
     * php artisan area-make:class Test7 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --property="protected \$test = 'test';"
     * php artisan area-make:class Test8 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     * php artisan area-make:class Test9 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';"
     * php artisan area-make:class Test10 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     * php artisan area-make:class Test11 --path=vvv --confirm-overwrite --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     *
     * php artisan area-make:class Test12 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';"
     * php artisan area-make:class Test13 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     * php artisan area-make:class Test14 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     * php artisan area-make:class Test15 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     *
     * php artisan area-make:class Test16 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     *
     * php artisan area-make:class Test17 --path=vvv --confirm-overwrite --abstract
     * php artisan area-make:class Test17 --path=vvv --confirm-overwrite --abstract
     * php artisan area-make:class Test17 --path=vvv --confirm-overwrite --abstract --parent=Illuminate\\Database\\Eloquent\\Model
     * php artisan area-make:class Test17 --path=vvv --confirm-overwrite --abstract --interface=Illuminate\\Contracts\\Queue\\ShouldQueue
     * php artisan area-make:class Test17 --path=vvv --confirm-overwrite --abstract --parent=Illuminate\\Database\\Eloquent\\Model --interface=Illuminate\\Contracts\\Queue\\ShouldQueue

     *
     *
     * php artisan area-make:class Test1 --path=vvv --confirm-overwrite &&  php artisan area-make:class Test2 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" && php artisan area-make:class Test3 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" && php artisan area-make:class Test4 --path=vvv --confirm-overwrite --property="protected \$test = 'test';" && php artisan area-make:class Test5 --path=vvv --confirm-overwrite --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:class Test6 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" && php artisan area-make:class Test7 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --property="protected \$test = 'test';" && php artisan area-make:class Test8 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:class Test9 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" && php artisan area-make:class Test10 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:class Test11 --path=vvv --confirm-overwrite --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:class Test12 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" && php artisan area-make:class Test13 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:class Test14 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:class Test15 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"&& php artisan area-make:class Test16 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     *
     * php artisan area-make:test Test1 --path=vvv --confirm-overwrite
     *
     * php artisan area-make:test Test2 --path=vvv --confirm-overwrite --trait="TestClass"
     * php artisan area-make:test Test3 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];"
     * php artisan area-make:test Test4 --path=vvv --confirm-overwrite --property="protected \$test = 'test';"
     * php artisan area-make:test Test5 --path=vvv --confirm-overwrite --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     *
     * php artisan area-make:test Test6 --path=vvv --confirm-overwrite --trait="TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];"
     * php artisan area-make:test Test7 --path=vvv --confirm-overwrite --trait="TestClass" --property="protected \$test = 'test';"
     * php artisan area-make:test Test8 --path=vvv --confirm-overwrite --trait="TestClass" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     * php artisan area-make:test Test9 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';"
     * php artisan area-make:test Test10 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     * php artisan area-make:test Test11 --path=vvv --confirm-overwrite --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     *
     * php artisan area-make:test Test12 --path=vvv --confirm-overwrite --trait="TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';"
     * php artisan area-make:test Test13 --path=vvv --confirm-overwrite --trait="TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     * php artisan area-make:test Test14 --path=vvv --confirm-overwrite --trait="TestClass" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     * php artisan area-make:test Test15 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     *
     * php artisan area-make:test Test16 --path=vvv --confirm-overwrite --trait="TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     *
     * php artisan area-make:test Test17 --path=vvv --confirm-overwrite --abstract
     * php artisan area-make:test Test17 --path=vvv --confirm-overwrite --abstract
     * php artisan area-make:test Test17 --path=vvv --confirm-overwrite --abstract --parent=Illuminate\\Database\\Eloquent\\Model
     * php artisan area-make:test Test17 --path=vvv --confirm-overwrite --abstract --interface=Illuminate\\Contracts\\Queue\\ShouldQueue
     * php artisan area-make:test Test17 --path=vvv --confirm-overwrite --abstract --parent=Illuminate\\Database\\Eloquent\\Model --interface=Illuminate\\Contracts\\Queue\\ShouldQueue

     * php artisan area-make:test Test21 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass,TestNamespace2\TestClass"
     * php artisan area-make:test Test21 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass,TestNamespace2\TestClass"
     * php artisan area-make:test Test22 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass,TestNamespace2\TestClass,TestNamespace3\TestNamespace2TestClass"
     * php artisan area-make:test Test23 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass,TestNamespace3\TestNamespace2TestClass,TestNamespace2\TestClass"
     * php artisan area-make:test Test24 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass,TestNamespace2\TestClass,TestNamespace3\TestNamespace2TestClass,Vvv\TestClass"
     * php artisan area-make:test Test25 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass,TestNamespace3\TestNamespace2TestClass,TestNamespace2\TestClass,Vvv\TestClass"
     *
     *
     * php artisan area-make:test Test1 --path=vvv --confirm-overwrite &&  php artisan area-make:test Test2 --path=vvv --confirm-overwrite --trait="TestClass" && php artisan area-make:test Test3 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" && php artisan area-make:test Test4 --path=vvv --confirm-overwrite --property="protected \$test = 'test';" && php artisan area-make:test Test5 --path=vvv --confirm-overwrite --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test6 --path=vvv --confirm-overwrite --trait="TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" && php artisan area-make:test Test7 --path=vvv --confirm-overwrite --trait="TestClass" --property="protected \$test = 'test';" && php artisan area-make:test Test8 --path=vvv --confirm-overwrite --trait="TestClass" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test9 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" && php artisan area-make:test Test10 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test11 --path=vvv --confirm-overwrite --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test12 --path=vvv --confirm-overwrite --trait="TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" && php artisan area-make:test Test13 --path=vvv --confirm-overwrite --trait="TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test14 --path=vvv --confirm-overwrite --trait="TestClass" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test15 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"&& php artisan area-make:test Test16 --path=vvv --confirm-overwrite --trait="TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"
     * php artisan area-make:test Test1 --path=vvv --confirm-overwrite &&  php artisan area-make:test Test2 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" && php artisan area-make:test Test3 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" && php artisan area-make:test Test4 --path=vvv --confirm-overwrite --property="protected \$test = 'test';" && php artisan area-make:test Test5 --path=vvv --confirm-overwrite --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test6 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" && php artisan area-make:test Test7 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --property="protected \$test = 'test';" && php artisan area-make:test Test8 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test9 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" && php artisan area-make:test Test10 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test11 --path=vvv --confirm-overwrite --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test12 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" && php artisan area-make:test Test13 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test14 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }" && php artisan area-make:test Test15 --path=vvv --confirm-overwrite --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"&& php artisan area-make:test Test16 --path=vvv --confirm-overwrite --trait="TestNamespace\TestClass" --constant="const TEST = [PHP_EOL        'test',PHP_EOL    ];" --property="protected \$test = 'test';" --method="public function test()PHP_EOL    {PHP_EOL        return true;PHP_EOL    }"

     *
     */

    /**
     * @var string
     */
    public $instance = 'class';

    /**
     * @var string
     */
    public $stub = 'general' . DIRECTORY_SEPARATOR . 'class.stub';

    /**
     * @var array
     */
    public $keywords = [
        'namespace',
        'use',
        'abstract' => 'abstract',
        'pattern',
        'parent',
        'interface',
        'trait',
        'constant',
        'property',
        'method',
    ];

    /**
     * @var bool
     */
    public $makeBase = true;

    /**
     * @var array
     */
    public $baseImmutableKeywords = [
        '__parent',
        '__abstract',
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
    public $processKeywordBackSlashParts = [
        'parent' => [
            '\Illuminate\Support\Str::camel',
            'ucfirst',
        ],
        'interface' => [
            '\Illuminate\Support\Str::camel',
            'ucfirst',
        ],
        'trait' => [
            '\Illuminate\Support\Str::camel',
            'ucfirst',
        ],
    ];

    /**
     * @param $stubContent
     * @return int|null
     * @throws \LaraAreaMake\Exceptions\LaraAreaCommandException
     */
    public function createBaseParent($stubContent)
    {
        $result = parent::createBaseParent($stubContent);

        if ($this->moveTraitsInterfacesToBase) {
            $this->__interface = [];
            $this->__trait = [];
        }

        return $result;
    }
}
