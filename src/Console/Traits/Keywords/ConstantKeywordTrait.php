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
            return $this->replaceContent(PHP_EOL .TAB . $keyword . PHP_EOL, '', $content);
        }

        if (is_string($input)) {
            $input = $this->processSpecialSymbols($input, 'PHP_EOL', PHP_EOL);
            return $this->replaceContent($keyword, $input, $content);
        }

        dd('TODO constant');
        $input = (array) $input;
        // @TODO generalize it and processInputIsArray check in dynamicallyParseOptionInput

        $data = [];
        foreach ($input as $constant => $input) {
            // @TODO fix
            $template = $this->parser->parseAttribute($constant, $input, '=', ';', 2);
            $template = \Illuminate\Support\Str::replaceFirst('$', '', $template);
            $template = 'const ' . $template;
            $data[] = $template;
        }

        $str = implode(PHP_EOL . PHP_EOL . TAB, $data);

        return $this->replaceContent($keyword , $str, $content);
    }
}