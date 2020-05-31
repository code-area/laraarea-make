<?php

namespace LaraAreaMake\Console\Traits\Keywords;

trait ConstantKeywordTrait
{
    /**
     * @var
     */
    protected $__constant;

    /**
     * @param $content
     * @param $keyword
     * @param $input
     * @return mixed
     */
    public function replaceConstantKeyword($content, $keyword, $input)
    {
        if (empty($input)) {
            return $this->replaceContent(TAB . PHP_EOL . TAB . $keyword . PHP_EOL, '', $content);
        }

        if (is_string($input)) {
            $input = $this->processSpecialSymbols($input, 'PHP_EOL', PHP_EOL);
            return $this->replaceContent($keyword, $input, $content);
        }

        dd('TODO constant');
        $input = (array) $input;
        // @TODO generalize it and processInputIsArray check in dynamicallyParseOptionInput

        $str = '';
        foreach ($input as $constant => $input) {
            // @TODO fix
            $template = $this->parser->parseAttribute($constant, $input, '=', ';', 2);
            $template = \Illuminate\Support\Str::replaceFirst('$', '', $template);
            $template = 'const ' . $template .PHP_EOL . PHP_EOL . TAB;
            $str .= $template;
        }


        $str = rtrim($str, PHP_EOL . PHP_EOL . TAB);

        return $this->replaceContent($keyword , $str, $content);
    }
}