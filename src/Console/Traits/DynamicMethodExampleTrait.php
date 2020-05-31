<?php

namespace LaraAreaMake\Console\Traits;

trait DynamicMethodExampleTrait
{
    /**
     * @var array
     */
    protected $keywords = [
        'example'
    ];

    /**
     * @var array
     */
    public $guessKeywords = [
        'example'
    ];

    /**
     * Firstly called this method for process "example" keyword
     *
     * @param $input
     * @return mixed
     */
    public function processExampleInput($input)
    {
        // some changes in input end return result
        return $input;
    }

    /**
     * if example in guessKeywords options
     * Firstly called this method for process "example" keyword
     *
     * @param $input
     * @return mixed
     */
    public function guessExampleKeyword($input)
    {
        // some changes in input end return result
        return $input;
    }

    /**
     * Firstly called this method for dynamically change keyword in stub content
     *  always use replaceContent method
     *
     * @param $content
     * @param $keyword
     * @param $input
     * @return mixed
     */
    public function replaceExampleKeyword($content, $keyword, $input)
    {
        // change in content $keyword based $input , or any type and return result
        return $this->replaceContent($keyword, $input, $content);
    }

    /**
     * if defined replaceExampleKeyword method this one must never executed
     * if not defined replaceExampleKeyword method than that case you can pass structure
     * according with must be change in stub contnt
     *
     * @param $keyword
     * @return string
     */
    public function replaceRepeatableExampleKeyword($keyword)
    {
        $template = 'some %s template';
        // $template = 'return %s;';
        // $template = 'return %s;' . PHP_EOL . $keyword; //when keyword can dynamically add new values
        return $template;
    }


    /**
     * Called this method if after replace remained $keyword and must be trim it
     *
     * @param $content
     * @param $keyword
     * @return mixed
     */
    public function trimExampleKeyword($content, $keyword)
    {
        //$content = str_replace($keyword, '', $content);
        return $content;
    }
}
