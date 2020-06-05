<?php

namespace LaraAreaMake\Console\Traits\Keywords;

trait TraitKeywordTrait
{
    /**
     * @var
     */
    protected $__trait;

    /**
     * @var
     */
    public $traitNewLineCount = 3;

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

        if (strlen(implode($traits)) > 50) {
            $separator = ',' . PHP_EOL . TAB . TAB;
        } else {
            $separator =  (count($traits) > $this->traitNewLineCount) ? ',' . PHP_EOL . TAB . TAB : ', ';
        }

        $to = sprintf('use %s;', implode($separator, $traits));
        return $this->replaceContent($keyword, $to, $content);
    }

}