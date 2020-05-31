<?php

namespace LaraAreaMake\Console\Traits\Keywords;

use Illuminate\Support\Str;

trait UseKeywordTrait
{
    /**
     * @var
     */
    public $__use;

    /**
     * @var
     */
    protected $namespaces = [];

    /**
     * @var 
     */
    protected $shortNamespaces = [];

    /**
     * @param $input
     * @return array
     */
    public function processUseInput($input)
    {
        $_namespaces = $this->wrapArray($input);

        $namespaces = [];
        foreach ($_namespaces as $namespace) {
            $namespaces[] = $this->processNamespace($namespace, false, false);
        }

        return $namespaces;
    }

    /**
     * @param $keyword
     * @return string
     */
    public function replaceRepeatableUseKeyword($keyword)
    {
        return 'use %s;' . PHP_EOL . $keyword;
    }

    /**
     * @param $content
     * @param $keyword
     * @return mixed
     */
    public function trimUseKeyword($content, $keyword)
    {
        if (empty($this->namespaces)) {
            if (empty($this->__use)) {
                return $this->replaceContent(PHP_EOL . $keyword . PHP_EOL, '', $content);
            }

            return $this->replaceContent(PHP_EOL . $keyword, '', $content);
        }

        foreach ($this->namespaces as $namespace) {
            $template = $this->replaceRepeatableUseKeyword($keyword);

            if (\Illuminate\Support\Str::startsWith($namespace, $this->__namespace)) {
                $_namespace = \Illuminate\Support\Str::replaceFirst($this->__namespace . DIRECTORY_SEPARATOR, '', $namespace);
                if( false == strpos($_namespace, DIRECTORY_SEPARATOR)) {
                    // namespace and file namespace is same
                    continue;
                }
                 dd('TODO');
            }

            $change = sprintf($template, $namespace);
            $content = $this->replaceContent($keyword, $change, $content);
//
//                $to .= sprintf($template, $namespace);
        }

        $this->namespaces = [];
        $this->shortNamespaces = [];
        return $this->replaceContent(PHP_EOL . $keyword, '', $content);
    }

    /**
     * @param $namespace
     * @param bool $isShort
     * @param bool $isDynamicNamespace
     * @return string
     */
    protected function processNamespace($namespace, $isShort = true, $isDynamicNamespace = true)
    {
        $baseName = class_basename($namespace);
        if (empty($this->shortNamespaces[$baseName])) {
            $this->shortNamespaces[$baseName] = $namespace;
            if ($isDynamicNamespace) {
                $this->namespaces[] = $namespace;
            }
            return $isShort ? $baseName : $this->shortNamespaces[$baseName];
        }

        if (\Illuminate\Support\Str::startsWith($namespace, $this->__namespace)) {
            $_namespace = \Illuminate\Support\Str::replaceFirst($this->__namespace . DIRECTORY_SEPARATOR, '', $namespace);
            if( false == strpos($_namespace, DIRECTORY_SEPARATOR)) {
                $oldNamespace = $this->shortNamespaces[$baseName];
                if (\Illuminate\Support\Str::contains($oldNamespace, '/')) {
                    // @TODO
                }

                $this->shortNamespaces[$baseName] = $namespace;
                $isShortOld = Str::contains($oldNamespace, 'as');
                $newNamespace = $this->correctDuplicateNamespace($oldNamespace, $isShortOld);
                $key = array_search($oldNamespace, $this->namespaces);
                if ($key) {
                    $this->namespaces = $newNamespace;
                }

                return $isShort ? $baseName : $this->shortNamespaces[$baseName];
            }
            // @TODO
        }

        $_namespace = \Illuminate\Support\Str::replaceLast(DIRECTORY_SEPARATOR, '', $namespace);
        $namespace = $this->correctDuplicateNamespace($namespace, $isShort, $_namespace);
        if ($isDynamicNamespace) {
            $this->namespaces[] = $namespace;
        }

        return $namespace;
    }


    /**
     * @param $namespace
     * @param $isShort
     * @param null $_namespace
     * @return string
     */
    protected function correctDuplicateNamespace($namespace, $isShort, $_namespace = null)
    {
        $baseName = class_basename($_namespace);

        if (! empty($this->shortNamespaces[$baseName])) {
            $_namespace = \Illuminate\Support\Str::replaceLast(DIRECTORY_SEPARATOR, '', $_namespace);
            return $this->correctDuplicateNamespace($namespace, $isShort, $_namespace);
        }

        $this->shortNamespaces[$baseName] = $namespace . ' as ' . $baseName;
        return $isShort ? $baseName : $this->shortNamespaces[$baseName];
    }

}