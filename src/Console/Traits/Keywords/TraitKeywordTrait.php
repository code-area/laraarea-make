<?php

namespace LaraAreaMake\Console\Traits\Keywords;

trait TraitKeywordTrait
{
    /**
     * @var
     */
    protected $__trait;

    /**
     * @param $content
     * @param $keyword
     * @param $input
     * @return mixed
     */
    public function replaceTraitKeyword($content, $keyword, $input)
    {
        if (empty($input)) {
            return $this->replaceContent(PHP_EOL .TAB . $keyword . PHP_EOL, '', $content);
        }

        $_traits = $this->wrapArray($input);

        $traits = [];
        foreach ($_traits as $trait) {
            $traits[] = $this->processNamespace($trait);
        }

        $to = sprintf('use %s;', implode(', ', $traits));
        return $this->replaceContent($keyword, $to, $content);
    }

}