<?php

namespace LaraAreaMake\Console\Traits\Keywords;

trait InterfaceKeywordTrait
{
    /**
     * @var
     */
    protected $__interface;

    /**
     * @param $content
     * @param $keyword
     * @param $input
     * @return mixed
     */
    public function replaceInterfaceKeyword($content, $keyword, $input)
    {
        if (empty($input)) {
            return $this->replaceContent(' ' . $keyword, '', $content);
        }

        $_interfaces = $this->wrapArray($input);
        $interfaces = [];
        foreach ($_interfaces as $interface) {
            $interfaces[] = $this->processNamespace($interface);
        }

        $to = sprintf('implements %s', implode(', ', $interfaces));
        return $this->replaceContent($keyword, $to, $content);
    }

}