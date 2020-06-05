<?php

namespace LaraAreaMake\Console\Traits\Keywords;

use Illuminate\Support\Str;

trait NamespaceKeywordTrait
{
    /**
     * @var
     */
    public $__namespace;

    /**
     * @var array
     */
    public $noNamespacePaths = [
        'routes',
        'config',
    ];

    /**
     * @param $input
     * @return string
     */
    public function guessNamespaceKeyword($input = null)
    {
        $currentPath = $this->getCurrentPath();
        $path = \Illuminate\Support\Str::replaceFirst(base_path() . DIRECTORY_SEPARATOR, '', $currentPath);

        $path = \Illuminate\Support\Str::replaceLast($this->extension, '', $path);
        $paths = explode(DIRECTORY_SEPARATOR, $path);
        array_pop($paths);
        $path = implode(DIRECTORY_SEPARATOR, $paths);

        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        $prs4 = (array) data_get($composer, 'autoload.psr-4');
        foreach ($prs4 as $namespace => $namespacePath) {
            $namespacePath = str_replace("/", DIRECTORY_SEPARATOR, $namespacePath);
            if (\Illuminate\Support\Str::startsWith($path, $namespacePath)) {
                return \Illuminate\Support\Str::replaceFirst($namespacePath, $namespace, $path);
            }
        }

        $classMap = (array) data_get($composer, 'autoload.classmap');
        $path = str_replace('\\', '/', $path);
        if (in_array($path, $classMap)) {
            return null;
        }

        if (Str::startsWith($path, $this->noNamespacePaths)) {
            return null;
        }

		// TODO add in compose.json autoload.psr-4
        return ucfirst($path);
    }

    /**
     * @param $content
     * @param $keyword
     * @param $input
     * @return mixed
     */
    public function replaceNamespaceKeyword($content, $keyword, $input)
    {
        if (empty($input)) {
            return $this->replaceContent(PHP_EOL . $keyword . PHP_EOL, '', $content);
        }

        return $this->replaceContent($keyword, 'namespace ' . $input . ';', $content);
    }
}