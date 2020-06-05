<?php

namespace LaraAreaMake\Console\Abstracts;

use LaraAreaMake\Console\Traits\Keywords\MethodKeywordTrait;
use \LaraAreaMake\Exceptions\LaraAreaCommandException;

abstract class ExtendablePhpMaker extends PhpMaker
{
    use MethodKeywordTrait;

    /**
     * Make BaseTemplate which all templates must be extend
     *
     * @var
     */
    public $makeBase = false;

    /**
     * If has parent baseParent must be extend parent
     * Base pattern name which all class must be extends
     *
     * @var
     */
    public $baseParent;

    /**
     * @var
     */
    public $moveTraitsInterfacesToBase = false;

    /**
     * If baseParent not defined that case baseParent property will automatically created
     *  baseParent = basePrefix .  instance
     *
     * @var string
     */
    public $basePrefix = 'Base';

    /**
     * @var
     */
    protected $__parent;

    /**
     * @var
     */
    public $parent;

    /**
     * @var array
     */
    public $baseImmutableKeywords = [
        '__parent'
    ];

    /**
     * @return mixed|string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws LaraAreaCommandException
     * @throws \ReflectionException
     */
    public function getStubContent()
    {
        $content = parent::getStubContent();;
        $content = $this->replaceContent(PHP_EOL . PHP_EOL, PHP_EOL . TAB .  PHP_EOL, $content);
        $content = $this->replaceContent( '{{namespace}}' . PHP_EOL . TAB . PHP_EOL, '{{namespace}}' . PHP_EOL . PHP_EOL, $content);
        $content = $this->replaceContent( '{{use}}' . PHP_EOL . TAB . PHP_EOL, '{{use}}' . PHP_EOL . PHP_EOL, $content);
        return $content;
    }

    /**
     * @param $dbStructure
     * @param $content
     * @return bool
     * @throws LaraAreaCommandException
     */
    protected function makeBasedDb($dbStructure, $content)
    {
        $this->__confirm = true;
        $this->__confirmOverwrite = true;
        if ($this->makeBase) {
            if (false == $this->createBaseParent($content)) {
                // @TODO show dont saved message
                return false;
            }
        }

        parent::makeBasedDb($dbStructure, $content);
    }

    /**
     * @param $patterns
     * @param $stubContent
     * @return bool
     * @throws LaraAreaCommandException
     */
    public function fillPatterns($patterns, $stubContent)
    {
        if ($this->makeBase) {
            if (false == $this->createBaseParent($stubContent)) {
                // @TODO show dont saved message
                return false;
            }
        }
        return parent::fillPatterns($patterns, $stubContent);
    }

    /**
     * @param $stubContent
     * @return int|null
     * @throws LaraAreaCommandException
     */
    public function createBaseParent($stubContent)
    {
        $baseParent = $this->getBaseParent();
        if ($this->moveTraitsInterfacesToBase) {
            $result =  $this->createFileBy($baseParent, $stubContent);
            if ($result) {
                $this->__use = [];
                $baseParent = $this->getBaseParent();
                $this->__parent = $this->__namespace . DIRECTORY_SEPARATOR . $baseParent;
                $this->__method = [];
            }
        } else {
            $this->processKeywordsValuesForBase();
            $result = $this->createFileBy($baseParent, $stubContent);
            $baseParent = $this->getBaseParent();
            $this->__parent = $this->parent = $this->__namespace . DIRECTORY_SEPARATOR . $baseParent;

            foreach ($this->getKeywordValues() as $keyword => $value) {
                if ($keyword == '__parent') {
                    continue;
                }
                $this->{$keyword} = $value;
            }
        }

        return $result;
    }

    /**
     *
     */
    protected function processKeywordsValuesForBase()
    {
        foreach ($this->getKeywordValues() as $keyword => $value) {
            if (in_array($keyword, $this->getBaseImmutableKeywords())) {
                continue;
            }
            $this->{$keyword} = null;
        }
    }

    /**
     * @return array
     */
    protected function getBaseImmutableKeywords()
    {
        return $this->baseImmutableKeywords;
    }

    /**
     * @return string
     */
    public function getBaseParent()
    {
        if ($this->baseParent) {
            return $this->baseParent;
        }

        return ucfirst($this->basePrefix) . ucfirst($this->instance);
    }

    /**
     * @param $content
     * @param $keyword
     * @param $input
     * @return mixed
     */
    public function replacePatternKeyword($content, $keyword, $input)
    {
        if (strpos($input, DIRECTORY_SEPARATOR)) {
            $input = last(explode(DIRECTORY_SEPARATOR, $input));
        }

        return $this->replaceContent($keyword, $input, $content);
    }

    /**
     * @param $content
     * @param $keyword
     * @param $input
     * @return mixed
     */
    public function replaceParentKeyword($content, $keyword, $input)
    {
        $parent = $input ?? $this->parent;
        if (empty($parent)) {
            return $this->replaceContent(' ' . $keyword, '', $content);
        }

        $baseName = $this->processNamespace($parent);
        $to = sprintf('extends %s', $baseName);

        return $this->replaceContent($keyword, $to, $content);
    }
}
