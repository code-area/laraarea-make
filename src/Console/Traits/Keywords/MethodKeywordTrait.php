<?php

namespace LaraAreaMake\Console\Traits\Keywords;

use LaraAreaMake\Exceptions\LaraAreaCommandException;

trait MethodKeywordTrait
{
    /**
     * @var
     */
    protected $__method;

////        $methods = [
////            'public' => [
////                [
////                    'name' => '__construct',
////                    'arguments' => ['name', 'str' => 'sas'],
////                    'content' => 'return true;'
////                ]
////            ]
////        ];
//
//
    /**
     * @param $content
     * @param $keyword
     * @param $input
     * @return mixed
     * @throws LaraAreaCommandException
     */
    public function replaceMethodKeyword($content, $keyword, $input)
    {
        if (empty($input)) {
            if (empty($this->__trait) && empty($this->__constant) && empty($this->__property)) {
                return $this->replaceContent($keyword, '', $content);
            }

            return $this->replaceContent(TAB . PHP_EOL . TAB . $keyword . PHP_EOL, '', $content);
        }

        if (is_string($input)) {
            $input = $this->processSpecialSymbols($input, 'PHP_EOL', PHP_EOL);
            return $this->replaceContent($keyword, $input, $content);
        }

        $str = '';
        $methods = (array) $input;

        if (!empty($methods)) {
            $str = $this->insertMethods($methods);
        }

        $str = rtrim($str, PHP_EOL . PHP_EOL . TAB);
        return $this->replaceContent(TAB . $keyword, $str, $content);
    }

    /**
     * @param $methods
     * @return string
     * @throws LaraAreaCommandException
     */
    protected function insertMethods($methods)
    {
        $str = '';
        foreach ($methods as $type => $_methods) {
            foreach ($_methods as $methodData) {
                $str .= $this->insertMethodBased($type, $methodData);
            }
        }
        return $str;
    }

    /**
     * @param $type
     * @param $data
     * @return mixed
     * @throws LaraAreaCommandException
     */
    protected function insertMethodBased($type, $data)
    {
        if (empty($data['name'])) {
            $message = sprintf("The '%s' attribute must be contain function_type => arrays. Each array is sets of array ".
                ". Each array must be have 'name' key. Fix this standards in '%s' class", '$methods', get_class($this));
            throw new LaraAreaCommandException($message);
        }

        $name = $data['name'];
        $arguments = !empty($data['arguments']) ? $data['arguments'] : [];

        $content = !empty($data['content']) ? $data['content'] : '';;
        return $this->methodTemplate($type, $name, $arguments, $content);
    }

    /**
     * @param $arguments
     * @return string
     */
    protected function fixMethodArguments($arguments)
    {
        if (empty($arguments)) {
            return '';
        }

        $argumentContents = [];
        foreach ($arguments as $argument => $data) {
            if (is_numeric($argument)) {
                if (is_string($data) && \Illuminate\Support\Str::contains($data, ' ')) {
                    $arr = explode(' ', $data);
                    $class = $arr[0];
                    // check for type hinting
                    if (class_exists($class)) {
                        $class = $this->insertStubUse($class);
                    }
                    $class = class_basename($class);
                    $data = $class . ' ' . $arr[1];
                }
                $argumentContents[] = $this->parser->parseAttribute($data);
            } else {
                $argumentContents[] = $this->parser->parseAttribute($argument, $data);
            }
        }
        return implode(', ', $argumentContents);

    }

    /**
     * @param $content
     * @return string
     */
    protected function fixMethodContent($content)
    {
        return trim($content);
    }

    /**
     * @param $contents
     * @return string
     */
    protected function methodContentRowTemplate($contents)
    {
        if (!is_array($contents)) {
            $contents = [$contents];
        }

        $str = '';

        foreach ($contents as  $content) {
            $str .= sprintf('%s%s%s;%s',TAB, TAB, $content, PHP_EOL);
        }

        return $str;
    }
}