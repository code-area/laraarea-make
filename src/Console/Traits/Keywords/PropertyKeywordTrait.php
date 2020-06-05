<?php

namespace LaraAreaMake\Console\Traits\Keywords;

trait PropertyKeywordTrait
{
    /**
     * @var
     */
    protected $__property;

//        $property = [
//            'public' => [
//                 'name' => 'test',
//            ]
//        ];

    /**
     * @param $content
     * @param $keyword
     * @param $input
     * @return mixed
     */
    public function replacePropertyKeyword($content, $keyword, $input)
    {
        if (empty($input)) {
            return $this->replaceContent(PHP_EOL .TAB . $keyword . PHP_EOL, '', $content);
        }

        $str = '';
        if (is_string($input)) {
            $input = $this->processSpecialSymbols($input, 'PHP_EOL', PHP_EOL);
            return $this->replaceContent($keyword, $input, $content);
        }

        $property = (array) $input;

        if (!empty($property)) {
            $str = $this->insertProperties($property);
        }

        return $this->replaceContent($keyword, $str, $content);
    }

    /**
     * @param $properties
     * @return string
     */
    protected function insertProperties($properties)
    {
        $data = [];
        foreach ($properties as $type => $propertiesData) {
            foreach ($propertiesData as $property => $value) {
                if (is_numeric($property)) {
                    $property = $value;
                    $value = null;
                }
                $data[] = $this->insertPropertyBased($type, $property, $value);
            }
        }
        return implode(PHP_EOL . PHP_EOL . TAB, $data);
    }

    /**
     * @param $type
     * @param $property
     * @param null $value
     * @return string
     */
    protected function insertPropertyBased($type, $property, $value = null)
    {
        $propertyStr = $type . ' ';
        $propertyStr .= $this->parser->parseAttribute($property, $value, '=', ';', 2);
        return $propertyStr;
    }
//
//    /**
//     * @param $data
//     * @return string
//     */
//    protected function getArrayStructure($data)
//    {
//        $str = ' = [' . PHP_EOL;
//        foreach ($data as $key => $input) {
//            if (!is_numeric($key)) {
//                if (is_array($value)) {
//                    //TODO fix
//                    dd('TODO');
//                } else {
//                    if (is_string($value)) {
//                        $str .= TAB . TAB . "'$key' => '$value'," . PHP_EOL;
//                    } else {
//                        $str .= TAB . TAB . "'$key' => ";
//                        $str .= ($value === true) ? 'true' : 'false';
//                        $str .= ',' .  PHP_EOL;
//                    }
//                }
//            } else {
//                //TODO fix [47 => 2]
//                //TODO fix [\ConstCommentType::Property => 2]
//                if (is_numeric($value)) {
//                    $str .= TAB . TAB . "'$value',".  PHP_EOL;
//                } else {
//                    $str .= TAB . TAB . "'$key' => '$value',".  PHP_EOL;
//                }
//            }
//        }
//        $str .= TAB . ']';
//        return $str;
//    }


}