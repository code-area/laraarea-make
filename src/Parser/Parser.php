<?php

namespace LaraAreaMake\Parser;
use Illuminate\Support\Str;

class Parser
{
    const NOT_DATA = '__not__data__';

    /**
     * @var array
     */
    private $arrayInputConfig = [
        'starts' => '[',
        'ends' => ']',
        'delimiter' => ','
    ];
//
//    /**
//     * @var string
//     */
//    protected $dynamicParseInputStructure = 'parse%sInput';

    /**
     * @param $key
     * @param $value
     * @return array | string
     * @throws \LaraAreaMake\Exceptions\LaraAreaCommandException
     */
    public function parseInput($key, $value)
    {
        if (is_null($key)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_array($value)) {
            return $value; // TODO process
        }

        $starts = $this->arrayInputConfig['starts'];

        if (Str::startsWith($value, $starts)) {
            return lara_maker_array_decode($value);
        }

        return $value;
    }

    /**
     * @param $attribute
     * @param string $data
     * @param string $suffix
     * @param string $separator
     * @return string
     */
    public function parseAttribute($attribute, $data = self::NOT_DATA, $separator = '=', $suffix = '', $depth = 0)
    {
        if (! Str::contains($attribute, ' ')) {
            $attribute = '$' . $attribute;
        }

        if($data === self::NOT_DATA) {
            return $attribute . $suffix;
        }

        if (is_array($data)) {
            return $this->parseArrayAttribute($attribute, $data, $separator, $suffix, $depth);
        }

        return "$attribute $separator " . $this->fixValue($data) . $suffix;
    }

    /**
     * @param $value
     * @return string
     */
    protected function fixValue($value) {

        if (is_null($value)) {
            return 'null';
        }

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        if (is_string($value)) {
            return "'$value'";
        }

        if (is_numeric($value)) {
            return $value;
        }

        // TODO New type fix it', $value;
        return $value;
    }

    /**
     * @param $attribute
     * @param $data
     * @param string $separator
     * @param string $suffix
     * @param int $depth
     * @return string
     */
    public function parseArrayAttribute($attribute, $data, $separator = '=', $suffix = '', $depth = 0)
    {
        $result = $attribute . ' ' .$separator . ' [';

        if (empty($data)) {
            $this->fixDepth($result,$depth);
            $result .= PHP_EOL;
        } else {
            foreach ($data as $attribute => $value) {
                $this->fixDepth($result,$depth);
                if (is_numeric($attribute)) {
                    $result .= $this->fixValue($value) . ',';
                } else {
                    $result .= $this->parseAttribute("'$attribute'", $value, '=>', ',', $depth + 1);
                }
            }
        }

        if ($depth) {
            $this->fixDepth($result, $depth - 1);
        }
        $result .= ']' . $suffix;
        return $result;
    }

    /**
     * @param $string
     * @param bool $depth
     * @return string
     */
    protected function fixDepth(&$string, $depth = false)
    {
        if (! Str::endsWith($string, PHP_EOL)) {
            $string .= PHP_EOL;
        }
        if ($depth) {
            for ($i = 0; $i < $depth; $i++) {
                $string .= TAB;
            }
        }
        return $string;
    }
//
//    /**
//     * Parameter #0 [ <required> CodeAreaRepo\Criteria\Criteria $criteria ]
//     */
//    public function parseReflectionParameter($parameter)
//    {
//        $parameterString = $parameter->__toString() . PHP_EOL;
//        $parameterString = Str::between($parameterString, '>', ']');
//        return trim($parameterString);
//    }
}