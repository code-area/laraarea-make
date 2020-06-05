<?php

namespace LaraAreaMake\Console\Traits\Keywords;

trait AbstractKeywordTrait
{
    /**
     * @var
     */
    protected $__abstract;

    /**
     * @param $content
     * @param $keyword
     * @param $input
     * @return mixed
     */
    public function replaceAbstractKeyword($content, $keyword, $input)
    {
        if (true == $input) {
            return $this->replaceContent($keyword, 'abstract', $content);
        }

        return $this->replaceContent($keyword . ' ', '', $content);
    }
}